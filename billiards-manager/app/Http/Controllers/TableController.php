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

    public function showDetail($id)
    {
        $table = Table::with([
            'tableRate',
            'currentBill.user',
            'currentBill.billDetails.product',
            'currentBill.billDetails.combo',
            'currentBill.billTimeUsages' => function ($query) {
                $query->whereNull('end_time')
                    ->orWhere('end_time', '>', now()->subHours(24));
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

            // KIỂM TRA VÀ XỬ LÝ COMBO ĐÃ HẾT THỜI GIAN
            if ($table->currentBill->comboTimeUsages->count() > 0) {
                $activeCombo = $table->currentBill->comboTimeUsages
                    ->where('is_expired', false)
                    ->where('remaining_minutes', '>', 0)
                    ->first();

                $expiredCombo = $table->currentBill->comboTimeUsages
                    ->where('is_expired', true)
                    ->first();

                // Nếu có combo đã hết thời gian
                if (!$activeCombo && $expiredCombo) {
                    $timeInfo['mode'] = 'combo_ended';
                    $timeInfo['needs_switch'] = true;
                    $timeInfo['is_auto_stopped'] = is_null($expiredCombo->end_time) ? false : true;
                }
            }

            // Cập nhật tổng tiền real-time (chỉ cho bàn tính giờ)
            if ($table->currentBill->status === 'Open') {
                app(BillController::class)->calculateBillTotal($table->currentBill);
                $table->currentBill->refresh();
            }
        }

        return view('admin.tables.detail', compact('table', 'combos', 'products', 'timeInfo'));
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
                'bill_status' => 'none'
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
                'bill_status' => 'quick'
            ];
        }

        // Kiểm tra combo time trước (ưu tiên hiển thị combo time)
        $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', false)
            ->where('remaining_minutes', '>', 0)
            ->first();

        if ($activeComboTime) {
            return $this->calculateComboTimeInfo($activeComboTime, $hourlyRate);
        }

        // Kiểm tra regular time
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeRegularTime) {
            return $this->calculateRegularTimeInfo($activeRegularTime, $hourlyRate);
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
            'bill_status' => 'no_time'
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
            'bill_status' => 'combo'
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

            // Trừ thời gian đã pause trước đó (nếu có)
            $effectiveMinutes = $elapsedMinutes - ($regularTime->paused_duration ?? 0);
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
            'bill_status' => 'regular'
        ];
    }
}
