<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

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
}
