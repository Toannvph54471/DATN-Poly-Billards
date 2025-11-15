<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillTimeUsage;
use App\Models\Category;
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
        $query = Table::query();

        // Lọc theo status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Lọc theo type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('table_number', 'like', "%{$search}%")
                    ->orWhere('table_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        $tableRates = TableRate::where('status', 'Active')->get();
        $tables = Table::with('tableRate')->paginate(10);

        $statuses = [
            Table::STATUS_AVAILABLE => 'Available',
            Table::STATUS_OCCUPIED => 'Occupied',
            Table::STATUS_PAUSED => 'Paused',
            Table::STATUS_MAINTENANCE => 'Maintenance',
            Table::STATUS_RESERVED => 'Reserved',
        ];
        return view('admin.tables.index', compact(
            'tables',
            'statuses',
            'tableRates'
        ));
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
            'type' => 'required|string',
            'status' => 'required|string',
        ]);

        $table = Table::findOrFail($id);
        $table->update($request->only([
            'table_name',
            'table_number',
            'type',
            'status',
        ]));

        return redirect()->route('admin.tables.index')
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

        return redirect()->route('admin.tables.create')->with('success', 'Thêm bàn mới thành công!');
    }

    // Xóa mềm
    public function destroy($id)
    {
        $table = Table::find($id);

        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bàn'], 404);
        }

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

        // Tính toán thời gian hiện tại - CHO CẢ BÀN TÍNH GIỜ VÀ BÀN LẺ
        $timeInfo = [];
        if ($table->currentBill && in_array($table->currentBill->status, ['Open', 'quick'])) {
            $timeInfo = $this->calculateCurrentTimeInfo($table);

            // Cập nhật tổng tiền real-time (chỉ cho bàn tính giờ)
            if ($table->currentBill->status === 'Open') {
                app(BillController::class)->calculateBillTotal($table->currentBill);
                $table->currentBill->refresh();
            }
        }

        return view('admin.tables.detail', compact('table', 'combos', 'products', 'timeInfo'));
    }

    private function calculateCurrentTimeInfo($table)
    {
        // Sử dụng method getHourlyRate() từ Table model
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

        // Kiểm tra combo time trước (ưu tiên hơn)
        $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', false)
            ->where('remaining_minutes', '>', 0)
            ->first();

        if ($activeComboTime) {
            $comboInfo = $this->calculateComboTimeInfo($activeComboTime, $hourlyRate);
            $comboInfo['bill_status'] = 'combo';
            return $comboInfo;
        }

        // Kiểm tra regular time
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeRegularTime) {
            $regularInfo = $this->calculateRegularTimeInfo($activeRegularTime, $hourlyRate);
            $regularInfo['bill_status'] = 'regular';
            return $regularInfo;
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
            // Đang tạm dừng
            $end = Carbon::parse($comboTime->end_time);
            $elapsedMinutes = $start->diffInMinutes($end);
            $isPaused = true;
            $isRunning = false;
        } else {
            // Đang chạy
            $elapsedMinutes = $start->diffInMinutes(now());
            $isPaused = false;
            $isRunning = true;
        }

        $remainingMinutes = max(0, $comboTime->remaining_minutes - $elapsedMinutes);
        $isNearEnd = $remainingMinutes <= 30 && $remainingMinutes > 0;

        // Tính phí phát sinh nếu vượt quá thời gian combo
        $extraMinutes = max(0, $elapsedMinutes - $comboTime->total_minutes);
        $extraCost = $extraMinutes * ($hourlyRate / 60);

        return [
            'is_running' => $isRunning,
            'mode' => 'combo',
            'elapsed_minutes' => $elapsedMinutes,
            'current_cost' => $extraCost,
            'hourly_rate' => $hourlyRate,
            'total_minutes' => $comboTime->total_minutes,
            'remaining_minutes' => $remainingMinutes,
            'is_near_end' => $isNearEnd,
            'is_paused' => $isPaused,
            'paused_duration' => 0,
            'combo_id' => $comboTime->combo_id
        ];
    }

    private function calculateRegularTimeInfo($regularTime, $hourlyRate)
    {
        $start = Carbon::parse($regularTime->start_time);

        if ($regularTime->paused_at) {
            // Đang tạm dừng
            $pausedAt = Carbon::createFromTimestamp($regularTime->paused_at);
            $elapsedMinutes = $start->diffInMinutes($pausedAt);
            $isPaused = true;
            $isRunning = false;
        } else {
            // Đang chạy
            $elapsedMinutes = $start->diffInMinutes(now());
            $isPaused = false;
            $isRunning = true;
        }

        // Trừ thời gian đã tạm dừng trước đó
        $effectiveMinutes = $elapsedMinutes - ($regularTime->paused_duration ?? 0);
        $currentCost = max(0, $effectiveMinutes) * ($hourlyRate / 60);

        return [
            'is_running' => $isRunning,
            'mode' => 'regular',
            'elapsed_minutes' => $effectiveMinutes,
            'current_cost' => $currentCost,
            'hourly_rate' => $hourlyRate,
            'total_minutes' => 0,
            'remaining_minutes' => 0,
            'is_near_end' => false,
            'is_paused' => $isPaused,
            'paused_duration' => $regularTime->paused_duration ?? 0
        ];
    }
}
