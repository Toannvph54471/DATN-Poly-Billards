<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillTimeUsage;
use App\Models\Category;
use App\Models\Combo;
use App\Models\ComboTimeUsage;
use App\Models\Product;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    // Hiện thị
    public function index(Request $request)
    {
        $query = Table::with(['category']);

        // Lọc theo category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

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

        $tables = $query->latest()->paginate(20);
        $categories = Category::all();

        $tableTypes = [
            Table::TYPE_STANDARD => 'Standard',
            Table::TYPE_VIP => 'VIP',
            Table::TYPE_COMPETITION => 'Competition',
        ];

        $statuses = [
            Table::STATUS_AVAILABLE => 'Available',
            Table::STATUS_OCCUPIED => 'Occupied',
            Table::STATUS_PAUSED => 'Paused',
            Table::STATUS_MAINTENANCE => 'Maintenance',
            Table::STATUS_RESERVED => 'Reserved',
        ];

        return view('admin.tables.index', compact(
            'tables',
            'categories',
            'tableTypes',
            'statuses'
        ));
    }
    // hien thi form sua 
    public function edit($id)
    {
        $table = Table::findOrFail($id);
        return view('admin.tables.edit', compact('table'));
    }
    // xu ly update thong tin ban
    public function update(Request $request, $id)
    {
        $request->validate([
            'table_name' => 'required|string|max:255',
            'table_number' => 'required|string|max:50',
            'type' => 'required|string',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'required|string',
        ]);

        $table = Table::findOrFail($id);
        $table->update($request->only([
            'table_name',
            'table_number',
            'type',
            'hourly_rate',
            'status',
        ]));

        return redirect()->route('admin.tables.index')
            ->with('success', 'Cập nhật bàn thành công!');
    }


    public function create()
    {

        return view('admin.tables.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|unique:tables,table_number|max:10',
            'table_name' => 'required|max:255|unique:tables,table_name',
            'type' => 'required|in:standard,vip,competition',
            'hourly_rate' => 'required|numeric|min:0',
        ], [
            'table_number.unique' => 'Mã bàn đã tồn tại.',
            'table_name.unique' => 'Tên bàn đã tồn tại trong hệ thống.',
        ]);

        Table::create([
            'table_number' => $request->table_number,
            'table_name' => $request->table_name,
            'type' => $request->type,
            'status' => Table::STATUS_AVAILABLE,
            'hourly_rate' => $request->hourly_rate,
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
            'tableRate', // THAY category bằng tableRate
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

        // Tính toán thời gian hiện tại - CHỈ KHI LÀ BÀN TÍNH GIỜ
        $timeInfo = [];
        if ($table->currentBill && $table->currentBill->status === 'Open') { // Sửa 'open' thành 'Open'
            $timeInfo = $this->calculateCurrentTimeInfo($table);

            // Cập nhật tổng tiền real-time
            app(BillController::class)->calculateBillTotal($table->currentBill);
            $table->currentBill->refresh();
        }

        return view('admin.tables.detail', compact('table', 'combos', 'products', 'timeInfo'));
    }

    private function calculateCurrentTimeInfo($table)
    {
        if (!$table->currentBill || $table->currentBill->status !== 'Open') { // Sửa 'open' thành 'Open'
            return [
                'is_running' => false,
                'mode' => 'none',
                'elapsed_minutes' => 0,
                'current_cost' => 0,
                'hourly_rate' => $table->hourly_rate, // Sử dụng attribute
                'total_minutes' => 0,
                'is_near_end' => false,
                'is_paused' => false,
                'paused_duration' => 0,
                'remaining_minutes' => 0
            ];
        }

        $bill = $table->currentBill;
        $hourlyRate = $table->hourly_rate; // Sử dụng attribute

        // Kiểm tra combo time trước (ưu tiên hơn)
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
            'remaining_minutes' => 0
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
        $start = \Carbon\Carbon::parse($regularTime->start_time);

        if ($regularTime->paused_at) {
            // Đang tạm dừng
            $pausedAt = \Carbon\Carbon::createFromTimestamp($regularTime->paused_at);
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
