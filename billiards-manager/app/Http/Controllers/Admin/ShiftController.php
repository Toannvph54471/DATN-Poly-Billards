<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::all();
        return view('admin.shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('admin.shifts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
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
            'name' => 'required',
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
