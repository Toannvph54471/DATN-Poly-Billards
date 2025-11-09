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

    // BillController.php
    public function showDetail($id)
    {
        $table = Table::with([
            'category',
            'currentBill.customer',
            'currentBill.billDetails.product',
            'currentBill.billDetails.combo',
            'currentBill.billTimeUsages' => function ($query) {
                $query->whereNull('end_time')
                    ->orWhere('end_time', '>', now()->subHours(24));
            },
            'currentBill.comboTimeUsages' => function ($query) {
                $query->where('is_expired', false)
                    ->orWhere('end_time', '>', now()->subHours(24));
            }
        ])->findOrFail($id);

        // Lấy danh sách combo và products để hiển thị trong form
        $combos = Combo::where('status', 'active')->get();
        $products = Product::where('status', 'Active')->get();

        // Tính toán thời gian hiện tại
        $timeInfo = $this->calculateCurrentTimeInfo($table);

        return view('admin.tables.detail', compact('table', 'timeInfo', 'combos', 'products'));
    }

    // app/Http\Controllers/TableController.php

    private function calculateCurrentTimeInfo($table)
    {
        $timeInfo = [
            'mode' => 'none',
            'elapsed_minutes' => 0,
            'remaining_minutes' => 0,
            'total_minutes' => 0,
            'current_cost' => 0,
            'is_running' => false,
            'is_near_end' => false,
            'hourly_rate' => $table->category->hourly_rate ?? 0
        ];

        if (!$table->currentBill) {
            return $timeInfo;
        }

        $bill = $table->currentBill;

        // Kiểm tra combo time đang hoạt động
        $activeComboTime = $bill->comboTimeUsages
            ->where('is_expired', false)
            ->where('remaining_minutes', '>', 0)
            ->first();

        // Kiểm tra regular time đang hoạt động
        $activeRegularTime = $bill->billTimeUsages
            ->whereNull('end_time')
            ->first();

        if ($activeComboTime) {
            // Đang dùng COMBO TIME
            $elapsed = $activeComboTime->start_time->diffInMinutes(now());
            $remaining = max(0, $activeComboTime->remaining_minutes - $elapsed);

            // Tính current_cost dựa trên extra_minutes_added
            $currentCost = 0;
            if ($activeComboTime->extra_minutes_added > 0) {
                $tableRate = $table->category->hourly_rate;
                $currentCost = ($tableRate / 60) * $activeComboTime->extra_minutes_added;
            }

            $timeInfo = [
                'mode' => 'combo',
                'elapsed_minutes' => $elapsed,
                'remaining_minutes' => $remaining,
                'total_minutes' => $activeComboTime->total_minutes,
                'current_cost' => $currentCost,
                'is_running' => true,
                'is_near_end' => $remaining <= 10,
                'hourly_rate' => $table->category->hourly_rate ?? 0
            ];
        } elseif ($activeRegularTime) {
            // Đang dùng REGULAR TIME
            $elapsed = $activeRegularTime->start_time->diffInMinutes(now());
            $cost = ($activeRegularTime->hourly_rate / 60) * $elapsed;

            $timeInfo = [
                'mode' => 'regular',
                'elapsed_minutes' => $elapsed,
                'remaining_minutes' => 0,
                'total_minutes' => $elapsed,
                'current_cost' => $cost,
                'is_running' => true,
                'is_near_end' => false,
                'hourly_rate' => $activeRegularTime->hourly_rate
            ];

            
        }

        return $timeInfo;
    }
}
