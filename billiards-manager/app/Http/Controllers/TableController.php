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
