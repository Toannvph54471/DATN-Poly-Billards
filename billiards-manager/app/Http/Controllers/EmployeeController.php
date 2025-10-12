<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->latest()->paginate(10);
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        $users = User::doesntHave('employee')->get();
        return view('admin.employees.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'position' => 'required|string|max:255',
            'salary_rate' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'status' => 'required|string',
        ]);

        Employee::create($validated);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Thêm nhân viên thành công!');
    }

    public function edit(Employee $employee)
    {
        $users = User::all();
        return view('admin.employees.edit', compact('employee', 'users'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'position' => 'required|string|max:255',
            'salary_rate' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $employee->update($validated);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Cập nhật thông tin nhân viên thành công!');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')
            ->with('success', 'Xóa nhân viên thành công!');
    }
}
