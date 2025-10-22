<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        // Lấy tất cả các ca
        $shifts = Shift::with(['employees.user'])->get();

        return view('admin.shifts.index', compact('shifts'));
    }

    public function show($id)
    {
        // Xem danh sách nhân viên trong ca cụ thể
        $shift = Shift::with(['employees.user'])->findOrFail($id);
        return view('admin.shifts.show', compact('shift'));
    }
    public function create()
    {
        return view('admin.shifts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        Shift::create($request->only(['name', 'start_time', 'end_time']));

        return redirect()->route('admin.shifts.index')->with('success', 'Thêm ca làm thành công!');
    }

    public function edit(Shift $shift)
    {
        return view('admin.shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $shift->update($request->only(['name', 'start_time', 'end_time']));

        return redirect()->route('admin.shifts.index')->with('success', 'Cập nhật ca làm thành công!');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect()->route('admin.shifts.index')->with('success', 'Đã xóa ca làm!');
    }
}
