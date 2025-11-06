<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillTimeUsage;
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
        $query = Table::query();
        $types = Table::select('type')->distinct()->pluck('type');
        if ($request->filled('search')) {
            $query->where('table_name', 'like', "%{$request->search}%")
                ->orWhere('table_number', 'like', "%{$request->search}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tables = $query->paginate(10);

        return view('admin.tables.index', [
            'tables' => $tables,
            'types' => $types,
            'totalTables' => Table::count(),
            'inUseCount' => Table::where('status', 'in_use')->count(),
            'maintenanceCount' => Table::where('status', 'maintenance')->count(),
            'availableCount' => Table::where('status', 'available')->count(),
        ]);
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

    public function detail($id)
    {
        $table = Table::findOrFail($id);

        $products = Product::where('stock_quantity', '>', 0)
            ->select('id', 'name', 'price')
            ->get();

        $currentUsage = BillTimeUsage::whereHas('bill', function ($query) use ($id) {
            $query->where('table_id', $id)
                ->whereIn('status', ['Open', 'Paused']);
        })
            ->whereNull('end_time')
            ->with([
                'bill' => function ($q) {
                    $q->with(['staff', 'customer'])
                        ->with(['billDetails' => function ($q) {
                            $q->with(['product', 'combo']);
                        }]);
                }
            ])
            ->first(); // ← Vẫn first(), nhưng Blade sẽ kiểm tra null

        $usageHistory = BillTimeUsage::whereHas('bill', function ($query) use ($id) {
            $query->where('table_id', $id)->where('status', 'Closed');
        })
            ->whereNotNull('end_time')
            ->with('bill')
            ->orderBy('start_time', 'desc')
            ->take(50)
            ->get();

        $totalMinutes = $usageHistory->sum(fn($u) => $u->duration_minutes ?? 0);
        $totalRevenue = $usageHistory->sum(fn($u) => $u->bill->total_amount ?? 0);

        return view('admin.tables.detail', compact(
            'table',
            'currentUsage',
            'usageHistory',
            'totalMinutes',
            'totalRevenue',
            'products'
        ));
    }
}
