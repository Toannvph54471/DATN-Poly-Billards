<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillTimeUsage;
use App\Models\Combo;
use App\Models\ComboTimeUsage;
use App\Models\Product;
use App\Models\Table;
use App\Models\TableRate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TableController extends Controller
{
    // Hiện thị
    public function index(Request $request)
    {
        $query = Table::with([
            'tableRate',
            'currentBill',
            'currentBill.user',
            'currentBill.billTimeUsages' => function ($q) {
                $q->whereNull('end_time');
            },
            'currentBill.comboTimeUsages' => function ($q) {
                $q->where('is_expired', false)
                    ->where('remaining_minutes', '>', 0);
            }
        ]);

        // Filter theo search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('table_number', 'like', '%' . $request->search . '%')
                    ->orWhere('table_name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter theo status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter theo loại bàn
        if ($request->has('table_rate_id') && $request->table_rate_id) {
            $query->where('table_rate_id', $request->table_rate_id);
        }

        $tables = $query->paginate(20);

        // Tính toán thông tin thời gian cho mỗi bàn
        foreach ($tables as $table) {
            $table->time_info = $this->getTimeInfoForTable($table);
        }

        $tableRates = TableRate::where('status', 'Active')->get();
        $statuses = [
            'available' => 'Trống',
            'occupied' => 'Đang sử dụng',
            'paused' => 'Tạm dừng',
            'maintenance' => 'Bảo trì',
            'reserved' => 'Đã đặt',
            'quick' => 'Bàn lẻ'
        ];

        return view('admin.tables.index', compact('tables', 'tableRates', 'statuses'));
    }
    public function simpleDashboard()
    {
        try {
            // Lấy toàn bộ dữ liệu tables kèm bill hiện tại
            $tables = Table::with([
                'tableRate',
                'currentBill' => function ($query) {
                    $query->whereIn('status', ['Open', 'quick'])
                        ->with(['billTimeUsages', 'comboTimeUsages']);
                }
            ])->orderByRaw('ISNULL(position_y), position_y ASC')
                ->orderByRaw('ISNULL(position_x), position_x ASC')
                ->orderBy('table_number')
                ->get();

            // Thống kê
            $totalTables = $tables->count();

            // Phân loại bàn
            $availableTables = $tables->where('status', 'available');
            $occupiedTables = $tables->where('status', 'occupied');
            $quickTables = $tables->where('status', 'quick');
            $maintenanceTables = $tables->where('status', 'maintenance');

            // Tổng hợp tất cả bàn đang dùng (bao gồm cả quick)
            $allOccupiedTables = $tables->filter(function ($table) {
                return in_array($table->status, ['occupied', 'quick']);
            });

            // Lấy hóa đơn đang mở
            $openBills = Bill::whereIn('status', ['Open', 'quick'])
                ->with(['table'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Tính doanh thu hôm nay
            $todayRevenue = Bill::whereDate('created_at', today())
                ->where('status', 'Closed')
                ->sum('total_amount') ?? 0;

            // Tính toán các số liệu thống kê
            $availableCount = $availableTables->count();
            $occupiedCount = $occupiedTables->count();
            $quickCount = $quickTables->count();
            $maintenanceCount = $maintenanceTables->count();
            $totalOccupiedCount = $occupiedCount + $quickCount;

            // Tính occupancy rate
            $occupancyRate = $totalTables > 0
                ? round(($totalOccupiedCount / $totalTables) * 100)
                : 0;

            $stats = [
                'total' => $totalTables,
                'available' => $availableCount,
                'occupied' => $occupiedCount,
                'quick' => $quickCount,
                'maintenance' => $maintenanceCount,
                'total_occupied' => $totalOccupiedCount,
                'open_bills' => $openBills->count(),
                'today_revenue' => $todayRevenue,
                'occupancy_rate' => $occupancyRate
            ];

            // Mảng lưu các bàn cần cảnh báo
            $unprocessedTables = []; // Bàn combo đã hết nhưng chưa xử lý

            // Format table data với tính toán elapsed time
            $formattedTables = $tables->map(function ($table) use (&$unprocessedTables) {
                $elapsedTime = null;
                $hasCombo = false;
                $comboRemaining = null;
                $isUnprocessed = false;

                // TÍNH TOÁN THỜI GIAN GIỜ THƯỜNG
                if ($table->currentBill) {
                    // Tính thời gian đã sử dụng (giờ thường)
                    $elapsedTime = $this->calculateElapsedTime($table->currentBill);

                    // Kiểm tra combo
                    $hasCombo = $table->currentBill->comboTimeUsages()->exists();

                    if ($hasCombo) {
                        // Lấy tất cả combo, bao gồm cả đã hết hạn
                        $allCombos = $table->currentBill->comboTimeUsages()
                            ->orderBy('remaining_minutes')
                            ->get();

                        if ($allCombos->isNotEmpty()) {
                            // Tìm combo chưa hết hạn (is_expired = 0 và còn thời gian)
                            $activeCombo = $allCombos->first(function ($combo) {
                                return $combo->is_expired == 0 && $combo->remaining_minutes > 0;
                            });

                            if ($activeCombo) {
                                // Có combo đang active và còn thời gian
                                $comboRemaining = max(0, $activeCombo->remaining_minutes); // Đảm bảo không âm

                                // KIỂM TRA THỜI GIAN CÒN LẠI THỰC TẾ
                                if (is_null($activeCombo->end_time)) {
                                    // Combo đang chạy, tính thời gian đã sử dụng
                                    $startTime = Carbon::parse($activeCombo->start_time);
                                    $elapsedComboMinutes = $startTime->diffInMinutes(now());
                                    $comboRemaining = max(0, $activeCombo->total_minutes - $elapsedComboMinutes);
                                } else {
                                    // Combo đã tạm dừng, sử dụng remaining_minutes
                                    $comboRemaining = max(0, $activeCombo->remaining_minutes);
                                }
                            } else {
                                // Không có combo active, kiểm tra xem có combo đã hết nhưng chưa xử lý không
                                $expiredCombo = $allCombos->first(function ($combo) {
                                    // Combo đã hết thời gian (remaining_minutes <= 0) nhưng chưa đánh dấu expired
                                    return $combo->is_expired == 0 && $combo->remaining_minutes <= 0;
                                });

                                if ($expiredCombo) {
                                    // Combo đã hết thời gian nhưng chưa đánh dấu expired
                                    $isUnprocessed = true;
                                    $comboRemaining = 0;
                                    $unprocessedTables[] = [
                                        'id' => $table->id,
                                        'table_number' => $table->table_number,
                                        'table_name' => $table->table_name ?? "Bàn {$table->table_number}"
                                    ];
                                }
                            }
                        }
                    }
                }

                return [
                    'id' => $table->id,
                    'table_number' => $table->table_number,
                    'table_name' => $table->table_name ?? "Bàn {$table->table_number}",
                    'capacity' => $table->capacity,
                    'status' => $table->status,
                    'hourly_rate' => $table->getHourlyRate(),
                    'elapsed_time' => $elapsedTime,
                    'has_combo' => $hasCombo,
                    'combo_remaining' => $comboRemaining, // Thêm comboRemaining - LUÔN >= 0
                    'is_unprocessed' => $isUnprocessed, // Thêm is_unprocessed
                    'position_x' => $table->position_x,
                    'position_y' => $table->position_y,
                    'z_index' => $table->z_index
                ];
            });

            return view('admin.tables.simple-dashboard', [
                'stats' => $stats,
                'tables' => $formattedTables,
                'openBills' => $openBills,
                'availableTables' => $availableTables,
                'occupiedTables' => $occupiedTables,
                'quickTables' => $quickTables,
                'maintenanceTables' => $maintenanceTables,
                'unprocessedTables' => $unprocessedTables, // Truyền thêm dữ liệu này
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());

            return view('admin.tables.simple-dashboard', [
                'stats' => [
                    'total' => 0,
                    'available' => 0,
                    'occupied' => 0,
                    'quick' => 0,
                    'maintenance' => 0,
                    'total_occupied' => 0,
                    'open_bills' => 0,
                    'today_revenue' => 0,
                    'occupancy_rate' => 0
                ],
                'tables' => collect(),
                'openBills' => collect(),
                'availableTables' => collect(),
                'occupiedTables' => collect(),
                'quickTables' => collect(),
                'maintenanceTables' => collect(),
                'unprocessedTables' => [],
                'error' => 'Lỗi khi tải dữ liệu: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Tính thời gian đã sử dụng của bill
     */
    private function calculateElapsedTime(Bill $bill)
    {
        $elapsedMinutes = 0;

        if ($bill->status === 'quick') {
            // Bàn lẻ không tính giờ
            return null;
        }

        // Tính tổng thời gian từ tất cả các session giờ thường
        foreach ($bill->billTimeUsages as $timeUsage) {
            if (is_null($timeUsage->end_time)) {
                // Session đang chạy
                $start = Carbon::parse($timeUsage->start_time);
                $elapsedMinutes += $start->diffInMinutes(now());
            } else {
                // Session đã kết thúc
                $start = Carbon::parse($timeUsage->start_time);
                $end = Carbon::parse($timeUsage->end_time);
                $elapsedMinutes += $start->diffInMinutes($end);
            }
        }

        if ($elapsedMinutes <= 0) {
            return null;
        }

        // Format thời gian
        $hours = floor($elapsedMinutes / 60);
        $minutes = $elapsedMinutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h{$minutes}p";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}p";
        }
    }



    public function saveLayout(Request $request)
    {
        try {
            $positions = $request->input('positions', []);

            foreach ($positions as $tableId => $position) {
                Table::where('id', $tableId)->update([
                    'position_x' => $position['x'],
                    'position_y' => $position['y'],
                    'z_index' => $position['z'] ?? 0,
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu bố cục thành công!'
            ]);
        } catch (\Exception $e) {
            Log::error('Save layout error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lưu bố cục'
            ], 500);
        }
    }

    public function resetLayout()
    {
        try {
            // Reset tất cả vị trí về null
            Table::query()->update([
                'position_x' => null,
                'position_y' => null,
                'z_index' => 0,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã reset bố cục về mặc định!'
            ]);
        } catch (\Exception $e) {
            Log::error('Reset layout error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi reset bố cục'
            ], 500);
        }
    }


    // hien thi form sua 
    public function edit($id)
    {
        $table = Table::findOrFail($id);
        $tableRates = TableRate::where('status', 'Active')->get();
        return view('admin.tables.edit', compact('table', 'tableRates'));
    }
    // xu ly update thong tin ban
    public function update(Request $request, $id)
    {
        $request->validate([
            'table_name' => 'required|string|max:255',
            'table_number' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|string',
            'table_rate_id' => 'required|exists:table_rates,id',
        ]);

        $table = Table::findOrFail($id);

        $table->update($request->only([
            'table_name',
            'table_number',
            'capacity',
            'status',
            'table_rate_id',
        ]));

        return redirect()->route('admin.tables.edit', $table->id)
            ->with('success', 'Cập nhật bàn thành công!');
    }



    // Hiển thị form thêm bàn
    public function create()
    {
        $tableRates = TableRate::where('status', 'Active')->get();
        return view('admin.tables.create', compact('tableRates'));
    }

    // Lưu bàn mới
    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|max:10|unique:tables,table_number',
            'table_name' => 'required|string|max:255|unique:tables,table_name',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,maintenance',
            'table_rate_id' => 'required|exists:table_rates,id',
        ], [
            'table_number.unique' => 'Mã bàn đã tồn tại.',
            'table_name.unique' => 'Tên bàn đã tồn tại trong hệ thống.',
        ]);

        Table::create([
            'table_number' => $request->table_number,
            'table_name' => $request->table_name,
            'capacity' => $request->capacity,
            'status' => $request->status,
            'table_rate_id' => $request->table_rate_id,
        ]);

        return redirect()->route('admin.tables.index')->with('success', 'Thêm bàn mới thành công!');
    }

    // Xóa mềm
    public function destroy($id)
    {
        $table = Table::find($id);

        if (!$table) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bàn'
            ], 404);
        }

        // Kiểm tra bàn có bill đang hoạt động hay không
        $isInUse = $table->bills()
            ->whereIn('status', ['open', 'playing', 'quick'])
            ->exists();

        if ($isInUse) {
            return response()->json([
                'success' => false,
                'message' => 'Bàn đang được sử dụng, không thể xóa'
            ], 400);
        }

        // Nếu không sử dụng -> được phép xóa
        $table->delete();

        return response()->json(['success' => true]);
    }



    public function trashed()
    {
        $tables = Table::onlyTrashed()->get();
        return view('admin.tables.trashed', compact('tables'));
    }

    public function restore($id)
    {
        $table = Table::onlyTrashed()->where('id', $id)->firstOrFail();
        $table->restore();
        return redirect()->route('admin.tables.trashed')->with('success', 'Khôi phục bàn thành công!');
    }

    // Thêm vào controller
    public function showDetail($id)
    {
        $table = Table::with([
            'tableRate',
            'currentBill.user',
            'currentBill.billDetails.product',
            'currentBill.billDetails.combo',
            'currentBill.billTimeUsages' => function ($query) {
                $query->whereNull('end_time')
                    ->orWhere('end_time', '>', now()->subHours(24))
                    ->with(['bill' => function ($q) {
                        $q->with('table'); // Load table thông qua bill
                    }]);
            },
            'currentBill.comboTimeUsages' => function ($query) {
                $query->where(function ($q) {
                    $q->where('is_expired', false)
                        ->orWhere('end_time', '>', now()->subHours(24));
                });
            }
        ])->findOrFail($id);

        $combos = Combo::where('status', 'active')->get();
        $products = Product::where('status', 'Active')->get();

        // Tính toán thời gian hiện tại
        $timeInfo = [];
        if ($table->currentBill && in_array($table->currentBill->status, ['Open', 'quick'])) {
            $timeInfo = $this->calculateCurrentTimeInfo($table);

            // Cập nhật tổng tiền real-time (chỉ cho bàn tính giờ)
            if ($table->currentBill->status === 'Open') {
                app(BillController::class)->calculateBillTotal($table->currentBill);
                $table->currentBill->refresh();
            }
        }

        // Nếu có currentBill, lấy tất cả billTimeUsages của bill này
        $timeUsages = collect();
        if ($table->currentBill) {
            $timeUsages = $table->currentBill->billTimeUsages()
                ->where(function ($query) {
                    $query->whereNull('end_time')
                        ->orWhere('end_time', '>', now()->subHours(24));
                })
                ->with(['bill' => function ($q) {
                    $q->with('table');
                }])
                ->orderBy('start_time', 'asc')
                ->get();
        }

        return view('admin.tables.detail', compact('table', 'combos', 'products', 'timeInfo', 'timeUsages'));
    }

    // Thêm vào TableController
    public function getTimeInfoForTable($table)
    {
        if ($table->status === 'occupied' && $table->currentBill) {
            return $this->calculateCurrentTimeInfo($table);
        }

        return [
            'mode' => 'none',
            'remaining_minutes' => 0,
            'is_running' => false,
            'is_paused' => false
        ];
    }

    private function calculateCurrentTimeInfo($table)
    {
        $hourlyRate = $table->getHourlyRate();

        if (!$table->currentBill || !in_array($table->currentBill->status, ['Open', 'quick'])) {
            return [
                'is_running' => false,
                'mode' => 'none',
                'elapsed_minutes' => 0,
                'current_cost' => 0,
                'hourly_rate' => $hourlyRate,
                'total_minutes' => 0,
                'is_near_end' => false,
                'is_paused' => false,
                'paused_duration' => 0,
                'remaining_minutes' => 0,
                'bill_status' => 'none',
                'needs_switch' => false,
                'is_auto_stopped' => false
            ];
        }

        $bill = $table->currentBill;

        // Nếu là bàn lẻ (quick)
        if ($bill->status === 'quick') {
            return [
                'is_running' => false,
                'mode' => 'quick',
                'elapsed_minutes' => 0,
                'current_cost' => 0,
                'hourly_rate' => $hourlyRate,
                'total_minutes' => 0,
                'is_near_end' => false,
                'is_paused' => false,
                'paused_duration' => 0,
                'remaining_minutes' => 0,
                'bill_status' => 'quick',
                'needs_switch' => false,
                'is_auto_stopped' => false
            ];
        }

        // QUAN TRỌNG: Kiểm tra regular time TRƯỚC combo time
        // Nếu đã có giờ thường đang chạy, ưu tiên hiển thị giờ thường
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeRegularTime) {
            return $this->calculateRegularTimeInfo($activeRegularTime, $hourlyRate);
        }

        // Kiểm tra combo time đang active
        $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', false)
            ->where('remaining_minutes', '>', 0)
            ->first();

        if ($activeComboTime) {
            return $this->calculateComboTimeInfo($activeComboTime, $hourlyRate);
        }

        // Kiểm tra combo time đã hết hạn
        $expiredComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', true)
            ->first();

        if ($expiredComboTime) {
            // QUAN TRỌNG: SỬA LẠI - Chỉ kiểm tra giờ thường ĐANG CHẠY (chưa kết thúc)
            $hasActiveRegularTime = BillTimeUsage::where('bill_id', $bill->id)
                ->whereNull('end_time')
                ->exists();

            if ($hasActiveRegularTime) {
                // Đã có giờ thường ĐANG CHẠY, không hiển thị nút bật giờ thường
                return [
                    'is_running' => false,
                    'mode' => 'none',
                    'elapsed_minutes' => 0,
                    'current_cost' => 0,
                    'hourly_rate' => $hourlyRate,
                    'total_minutes' => 0,
                    'is_near_end' => false,
                    'is_paused' => false,
                    'paused_duration' => 0,
                    'remaining_minutes' => 0,
                    'bill_status' => 'no_time',
                    'needs_switch' => false, // Không cần chuyển vì đã có giờ thường đang chạy
                    'is_auto_stopped' => false
                ];
            }

            // Chưa có giờ thường đang chạy, hiển thị nút bật giờ thường
            $isAutoStopped = is_null($expiredComboTime->end_time) ||
                ($expiredComboTime->remaining_minutes <= 0 &&
                    Carbon::parse($expiredComboTime->start_time)->diffInMinutes(now()) >= $expiredComboTime->total_minutes);

            return [
                'is_running' => false,
                'mode' => 'combo_ended',
                'elapsed_minutes' => 0,
                'current_cost' => 0,
                'hourly_rate' => $hourlyRate,
                'total_minutes' => $expiredComboTime->total_minutes,
                'remaining_minutes' => 0,
                'is_near_end' => false,
                'is_paused' => false,
                'paused_duration' => 0,
                'combo_id' => $expiredComboTime->combo_id,
                'bill_status' => 'combo_ended',
                'needs_switch' => true, // Hiển thị nút bật giờ thường
                'is_auto_stopped' => $isAutoStopped
            ];
        }

        return [
            'is_running' => false,
            'mode' => 'none',
            'elapsed_minutes' => 0,
            'current_cost' => 0,
            'hourly_rate' => $hourlyRate,
            'total_minutes' => 0,
            'is_near_end' => false,
            'is_paused' => false,
            'paused_duration' => 0,
            'remaining_minutes' => 0,
            'bill_status' => 'no_time',
            'needs_switch' => false,
            'is_auto_stopped' => false
        ];
    }

    private function calculateComboTimeInfo($comboTime, $hourlyRate)
    {
        $start = Carbon::parse($comboTime->start_time);

        if ($comboTime->end_time) {
            // Đang tạm dừng - tính đến thời điểm tạm dừng
            $end = Carbon::parse($comboTime->end_time);
            $elapsedMinutes = $start->diffInMinutes($end);
            $isPaused = true;
            $isRunning = false;
        } else {
            // Đang chạy - tính đến hiện tại
            $elapsedMinutes = $start->diffInMinutes(now());
            $isPaused = false;
            $isRunning = true;
        }

        $remainingMinutes = max(0, $comboTime->remaining_minutes - $elapsedMinutes);
        $isNearEnd = $remainingMinutes <= 30 && $remainingMinutes > 0;

        return [
            'is_running' => $isRunning,
            'mode' => 'combo',
            'elapsed_minutes' => (int) round($elapsedMinutes),
            'current_cost' => 0, // Combo đã trả tiền trước
            'hourly_rate' => $hourlyRate,
            'total_minutes' => $comboTime->total_minutes,
            'remaining_minutes' => (int) round($remainingMinutes),
            'is_near_end' => $isNearEnd,
            'is_paused' => $isPaused,
            'paused_duration' => 0,
            'combo_id' => $comboTime->combo_id,
            'bill_status' => 'combo',
            'needs_switch' => false, // Thêm dòng này
            'is_auto_stopped' => false // Thêm dòng này
        ];
    }

    private function calculateRegularTimeInfo($regularTime, $hourlyRate)
    {
        $isPaused = !is_null($regularTime->paused_at);

        if ($isPaused) {
            // Đang tạm dừng - sử dụng paused_duration đã lưu
            $isRunning = false;
            $effectiveMinutes = $regularTime->paused_duration ?? 0;
        } else {
            // Đang chạy - tính từ start_time đến now
            $start = Carbon::parse($regularTime->start_time);
            $elapsedMinutes = $start->diffInMinutes(now());

            // KHÔNG trừ paused_duration nữa
            $effectiveMinutes = $elapsedMinutes;
            $isRunning = true;
        }

        // Tính chi phí hiện tại
        $currentCost = max(0, $effectiveMinutes) * ($hourlyRate / 60);

        return [
            'is_running' => $isRunning,
            'mode' => 'regular',
            'elapsed_minutes' => (int) round($effectiveMinutes),
            'current_cost' => $currentCost,
            'hourly_rate' => $hourlyRate,
            'total_minutes' => 0,
            'remaining_minutes' => 0,
            'is_near_end' => false,
            'is_paused' => $isPaused,
            'paused_duration' => $regularTime->paused_duration ?? 0,
            'bill_status' => 'regular',
            'needs_switch' => false,
            'is_auto_stopped' => false
        ];
    }

    /**
     * Tính thời gian đã sử dụng (đơn giản)
     */
    private function calculateSimpleElapsedTime($bill)
    {
        if ($bill->status === 'quick') {
            return 'BÀN LẺ';
        }

        // Kiểm tra combo time
        $activeComboTime = $bill->comboTimeUsages
            ->where('is_expired', false)
            ->where('remaining_minutes', '>', 0)
            ->whereNull('end_time')
            ->first();

        if ($activeComboTime) {
            $start = Carbon::parse($activeComboTime->start_time);
            $elapsedMinutes = $start->diffInMinutes(now());
            return sprintf('%02d:%02d', floor($elapsedMinutes / 60), $elapsedMinutes % 60);
        }

        // Kiểm tra regular time
        $activeRegularTime = $bill->billTimeUsages
            ->whereNull('end_time')
            ->first();

        if ($activeRegularTime) {
            $start = Carbon::parse($activeRegularTime->start_time);
            $elapsedMinutes = $start->diffInMinutes(now());

            if ($activeRegularTime->paused_duration) {
                $elapsedMinutes -= $activeRegularTime->paused_duration;
            }

            return sprintf('%02d:%02d', floor($elapsedMinutes / 60), $elapsedMinutes % 60);
        }

        return '--:--';
    }

    // TableController.php
    public function updatePositions(Request $request)
    {
        try {
            $positions = $request->input('positions', []);

            foreach ($positions as $position) {
                Table::where('id', $position['id'])->update([
                    'position_x' => $position['position_x'],
                    'position_y' => $position['position_y']
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Đã cập nhật vị trí bàn']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }
    public function pause(Table $table)
    {
        if ($table->status !== 'occupied') {
            return response()->json([
                'success' => false,
                'message' => 'Bàn không đang được sử dụng'
            ]);
        }

        DB::beginTransaction();
        try {
            // Cập nhật trạng thái bàn
            $table->status = 'paused';
            $table->save();

            // Nếu có bill đang chạy, cập nhật thời gian tạm dừng
            if ($table->currentBill) {
                // Lấy record time usage đang chạy
                $lastTimeUsage = $table->currentBill->billTimeUsages()
                    ->whereNull('end_time')
                    ->whereNull('paused_at') // Chỉ pause những cái chưa pause
                    ->latest()
                    ->first();

                if ($lastTimeUsage) {
                    // Tính thời gian đã chạy từ start_time đến lúc pause
                    $startTime = Carbon::parse($lastTimeUsage->start_time);
                    $elapsedMinutes = $startTime->diffInMinutes(now());

                    // Lưu paused_duration và paused_at
                    $lastTimeUsage->update([
                        'paused_duration' => $elapsedMinutes, // Lưu thời gian đã chạy
                        'paused_at' => now()->timestamp,      // Lưu thời điểm pause
                    ]);

                    Log::info('Paused time usage', [
                        'time_usage_id' => $lastTimeUsage->id,
                        'start_time' => $lastTimeUsage->start_time,
                        'elapsed_minutes' => $elapsedMinutes,
                        'paused_at' => now()
                    ]);
                } else {
                    Log::warning('No active time usage found for table', [
                        'table_id' => $table->id,
                        'bill_id' => $table->currentBill->id
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã tạm dừng bàn thành công',
                'status' => 'paused'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Pause table error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
    public function resume(Table $table)
    {
        if ($table->status !== 'paused') {
            return response()->json([
                'success' => false,
                'message' => 'Bàn không đang ở trạng thái tạm dừng'
            ]);
        }

        DB::beginTransaction();
        try {
            // Cập nhật trạng thái bàn
            $table->status = 'occupied';
            $table->save();

            // Nếu có bill đang chạy, tiếp tục session cũ
            if ($table->currentBill) {
                // Lấy record time usage đang tạm dừng
                $lastTimeUsage = $table->currentBill->billTimeUsages()
                    ->whereNotNull('paused_at')
                    ->whereNull('end_time')
                    ->latest()
                    ->first();

                if ($lastTimeUsage) {
                    // Tính tổng thời gian đã chạy (paused_duration)
                    $totalElapsedMinutes = $lastTimeUsage->paused_duration ?? 0;

                    // Cập nhật start_time để phản ánh thời gian đã chạy
                    $lastTimeUsage->update([
                        'start_time' => Carbon::now()->subMinutes($totalElapsedMinutes), // Điều chỉnh start_time
                        'paused_at' => null,   // Xóa thời điểm pause
                        'paused_duration' => $totalElapsedMinutes // Giữ nguyên để tham khảo
                    ]);

                    Log::info('Resumed time usage', [
                        'time_usage_id' => $lastTimeUsage->id,
                        'paused_duration' => $totalElapsedMinutes,
                        'new_start_time' => Carbon::now()->subMinutes($totalElapsedMinutes),
                        'resumed_at' => now()
                    ]);
                } else {
                    Log::warning('No paused time usage found for table', [
                        'table_id' => $table->id,
                        'bill_id' => $table->currentBill->id
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã tiếp tục bàn thành công',
                'status' => 'occupied'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Resume table error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Tính số phút đã sử dụng từ BillTimeUsage
     */
    private function calculateElapsedMinutes(BillTimeUsage $timeUsage): int
    {
        try {
            if (is_null($timeUsage->end_time)) {
                // Session chưa kết thúc
                $start = Carbon::parse($timeUsage->start_time);

                if ($timeUsage->paused_at) {
                    // Đang tạm dừng - trả về paused_duration
                    return (int) ($timeUsage->paused_duration ?? 0);
                } else {
                    // Đang chạy - tính từ start_time đến now
                    $elapsedMinutes = $start->diffInMinutes(now());
                    return $elapsedMinutes;
                }
            } else {
                // Session đã kết thúc - trả về duration_minutes
                return (int) ($timeUsage->duration_minutes ?? 0);
            }
        } catch (\Exception $e) {
            Log::error('Error in calculateElapsedMinutes: ' . $e->getMessage(), [
                'time_usage_id' => $timeUsage->id,
                'paused_at' => $timeUsage->paused_at,
                'paused_duration' => $timeUsage->paused_duration,
                'end_time' => $timeUsage->end_time
            ]);
            return 0;
        }
    }
}
