<?php

namespace App\Http\Controllers;

use App\Models\TableRate;
use App\Models\Category;
use Illuminate\Http\Request;

class TableRateController extends Controller
{
    public function index()
    {
        $rates = TableRate::query()->paginate(10);
        return view('admin.table_rates.index', compact('rates'));
    }

    public function create()
    {
        return view('admin.table_rates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:table_rates,code',
            'name' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:0',
            'max_hours' => 'required|integer|min:1',
            'status' => 'required|in:Active,Inactive',
        ]);

        TableRate::create($request->all());
        return redirect()->route('admin.table_rates.index')->with('success', 'Thêm bảng giá thành công!');
    }

    public function edit($id)
    {
        $rate = TableRate::findOrFail($id);
        return view('admin.table_rates.edit', compact('rate'));
    }

    public function update(Request $request, $id)
    {
        $rate = TableRate::findOrFail($id);
        $request->validate([
            'code' => 'required|unique:table_rates,code,' . $id,
            'name' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:0',
            'max_hours' => 'required|integer|min:1',
            'status' => 'required|in:Active,Inactive',
        ]);

        $rate->update($request->all());
        return redirect()->route('admin.table_rates.index')->with('success', 'Cập nhật bảng giá thành công!');
    }

    public function destroy($id)
    {
        $rate = TableRate::findOrFail($id);
        $rate->delete();
        return redirect()->route('admin.table_rates.index')->with('success', 'Đã xóa bảng giá (xóa mềm).');
    }
    // Hiển thị các bản ghi đã xóa (soft delete)
    public function trashed()
    {
        $rates = TableRate::onlyTrashed()->paginate(10); // chỉ lấy bản ghi đã xóa
        return view('admin.table_rates.trashed', compact('rates'));
    }

    // Khôi phục bản ghi đã xóa
    public function restore($id)
    {
        $rate = TableRate::onlyTrashed()->findOrFail($id);
        $rate->restore();
        return redirect()->route('admin.table_rates.trashed')->with('success', 'Khôi phục bảng giá thành công.');
    }

    // Xóa vĩnh viễn
    public function forceDelete($id)
    {
        $rate = TableRate::onlyTrashed()->findOrFail($id);
        $rate->forceDelete();
        return redirect()->route('admin.table_rates.trashed')->with('success', 'Xóa vĩnh viễn bảng giá thành công.');
    }
}
