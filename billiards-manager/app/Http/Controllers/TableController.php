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
            'totalTables' => Table::count(),
            'inUseCount' => Table::where('status', 'in_use')->count(),
            'maintenanceCount' => Table::where('status', 'maintenance')->count(),
            'availableCount' => Table::where('status', 'available')->count(),
        ]);
    }
    // hien thi form sua 
    public function edit($id){
        $table = Table::findOrFail($id);
        return view('admin.tables.test', compact('table'));
        
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

    
    // Xóa mềm
    public function destroy($id)
    {
        $table = Table::findOrFail($id);
        $table->delete(); // 
        return redirect()->route('admin.tables.index')->with('success', 'Bàn đã được xóa mềm!');
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
