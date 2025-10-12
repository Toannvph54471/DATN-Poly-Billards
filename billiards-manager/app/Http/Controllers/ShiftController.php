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
}
