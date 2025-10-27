<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use Illuminate\Http\Request;

class ComboController extends Controller
{
    public function index(Request $request)
    {
        $query = Combo::query();

        // Lọc theo mã combo
        if ($request->filled('code')) {
            $query->where('combo_code', 'like', '%' . $request->input('code') . '%');
        }

        // Lọc theo tên combo
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Thống kê
        $totalCombos = Combo::count();
        $activeCombos = Combo::where('status', 'Active')->count();
        $inactiveCombos = Combo::where('status', 'Inactive')->count();

        // Lấy danh sách combo với phân trang, giữ các tham số truy vấn
        $combos = $query->latest()->paginate(10)->appends($request->query());

        return view('admin.combos.index', compact('combos', 'totalCombos', 'activeCombos', 'inactiveCombos'));
    }

    public function create()
    {
        return view('admin.combos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'combo_code' => 'required|string|unique:combos,combo_code|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'actual_value' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
        ]);

        Combo::create($validated);

        return redirect()->route('admin.combos.index', $request->query())->with('success', 'Combo đã được tạo thành công.');
    }

    public function show($id)
    {
        $combo = Combo::findOrFail($id);
        return view('admin.combos.show', compact('combo'));
    }

    public function edit($id)
    {
        $combo = Combo::findOrFail($id);
        return view('admin.combos.edit', compact('combo'));
    }

    public function update(Request $request, $id)
    {
        $combo = Combo::findOrFail($id);

        $validated = $request->validate([
            'combo_code' => 'required|string|unique:combos,combo_code,' . $id . '|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'actual_value' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
        ]);

        $combo->update($validated);

        return redirect()->route('admin.combos.index', $request->query())->with('success', 'Combo đã được cập nhật thành công.');
    }

    public function destroy($id)
    {
        $combo = Combo::findOrFail($id);
        $combo->delete();

        return redirect()->route('admin.combos.index', request()->query())->with('success', 'Combo đã được xóa (soft delete).');
    }
 public function trashed()
{
    $combos = Combo::onlyTrashed()->latest()->paginate(10);
    return view('admin.combos.trashed', compact('combos'));
}
    public function restore($id)
{
    $combo = Combo::withTrashed()->findOrFail($id);
    $combo->restore();

    return redirect()->route('admin.combos.trashed')->with('success', 'Khôi phục combo thành công!');
}
public function forceDelete($id)
{
    $combo = Combo::withTrashed()->findOrFail($id);

    // Xóa các combo_items trước
    $combo->comboItems()->delete(); // nếu có quan hệ comboItems()

    $combo->forceDelete();

    return redirect()->route('admin.combos.trashed')->with('success', 'Combo và các mục liên quan đã bị xóa vĩnh viễn!');
}



}
