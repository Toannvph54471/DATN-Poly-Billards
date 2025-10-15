<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->has('code') && $request->code != '') {
            $query->where('employee_code', 'like', '%' . $request->code . '%');
        }

        if ($request->has('name') && $request->name != '') {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $statusLower = strtolower($request->status);
            $query->where('status', $statusLower === 'active' ? 0 : 1);
        }

        $employees = $query->with('user')->paginate(10);

        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 0)->count();
        $inactiveEmployees = Employee::where('status', 1)->count();
        $newEmployees = Employee::where('start_date', '>=', now()->subMonth())->count();

        return view('admin.employees.index', compact('employees', 'totalEmployees', 'activeEmployees', 'inactiveEmployees', 'newEmployees'));
    }
    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_code' => 'required|unique:employees,employee_code',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:employees,phone|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'address' => 'nullable|string|max:500',
            'position' => 'required|in:manager,staff,cashier,waiter',
            'salary_type' => 'required|in:hourly,monthly',
            'salary_rate' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Tìm role employee
        $role = Role::where('slug', User::ROLE_EMPLOYEE)->first();
        if (!$role) {
            Log::error('Role "employee" not found in roles table.');
            return redirect()->back()->withErrors(['role' => 'Role "employee" not found.'])->withInput();
        }

        // Tạo user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $role->id,
            'password' => Hash::make('nhanvien'),
            'status' => 'Active',
        ]);

        // Tạo employee
        $employeeData = $request->only(['employee_code', 'name', 'phone', 'email', 'address', 'position', 'salary_type', 'start_date', 'end_date']);
        $employeeData['user_id'] = $user->id;
        $employeeData['status'] = 0;
        $employeeData['salary_rate'] = $request->salary_rate;

        $employee = Employee::create($employeeData);

        // Log để kiểm tra
        Log::info('Created new employee: ' . json_encode($employee));

        return redirect()->route('admin.employees.index')->with('success', 'Nhân viên đã được thêm thành công.');
    }
    public function show($id)
    {
        $employee = Employee::with('employeeShifts.shift')->findOrFail($id);
        return view('admin.employees.show', compact('employee'));
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_code' => 'required|unique:employees,employee_code,' . $id,
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:employees,phone,' . $id . '|unique:users,phone,' . ($employee->user_id ?? 'NULL'),
            'email' => 'required|email|unique:users,email,' . ($employee->user_id ?? 'NULL'),
            'address' => 'nullable|string|max:500',
            'position' => 'required|in:manager,staff,cashier,waiter',
            'salary_type' => 'required|in:hourly,monthly',
            'salary_rate' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:0,1', // ĐÃ SỬA: chỉ cho phép 0 hoặc 1
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput(); // QUAN TRỌNG: giữ lại dữ liệu đã nhập
        }

        // Tìm role employee
        $employeeRole = Role::where('slug', User::ROLE_EMPLOYEE)->first();
        if (!$employeeRole) {
            return redirect()->back()->withErrors(['role' => 'Role "employee" not found.'])->withInput();
        }

        // Cập nhật user
        if ($employee->user_id) {
            $user = $employee->user;

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role_id' => $employeeRole->id, // LUÔN ĐẶT LÀ EMPLOYEE ROLE
                'status' => $request->status == 0 ? 'Active' : 'Inactive', // SỬA LOGIC STATUS
            ]);
        } else {
            // Tạo user mới nếu chưa có
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role_id' => $employeeRole->id,
                'password' => Hash::make(Str::random(8)),
                'status' => $request->status == 0 ? 'Active' : 'Inactive',
            ]);
            $employee->user_id = $user->id;
        }

        // Cập nhật employee
        $employee->update([
            'employee_code' => $request->employee_code,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'position' => $request->position,
            'salary_type' => $request->salary_type,
            'salary_rate' => $request->salary_rate,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'user_id' => $employee->user_id,
        ]);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Nhân viên đã được cập nhật thành công.');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        if ($employee->user) {
            $employee->user->delete(); // Xóa user liên kết
        }
        $employee->delete();
        Log::info('Deleted employee: ' . json_encode($employee)); // Log để debug
        return redirect()->route('admin.employees.index')->with('success', 'Nhân viên đã được xóa thành công.');
    }
}
