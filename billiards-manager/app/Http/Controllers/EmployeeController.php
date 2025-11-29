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
use Illuminate\Support\Facades\DB;

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
            $query->where('status', $request->status);
        }

        $employees = $query->paginate(10);

        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'Active')->count();
        $inactiveEmployees = Employee::where('status', 'Inactive')->count();
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
            'email' => 'required|email|unique:users,email|unique:employees,email',
            'address' => 'nullable|string|max:500',
            'position' => 'required|in:manager,staff,cashier,waiter',
            'salary_type' => 'required|in:hourly,monthly',
            'salary_rate' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Tìm role employee
            $role = Role::where('slug', User::ROLE_EMPLOYEE)->first();
            if (!$role) {
                throw new \Exception('Role "employee" not found.');
            }

            // Tạo user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role_id' => $role->id,
                'password' => Hash::make('bia123'), // Default password requested by user
                'status' => $request->status,
            ]);

            // Xác định salary_rate
            $salaryRate = $request->salary_rate;
            if (!$salaryRate) {
                $salaryRate = $request->salary_type === 'monthly' ? 35000.00 : 25000.00;
            }

            // Tạo employee
            $employee = Employee::create([
                'user_id' => $user->id,
                'employee_code' => $request->employee_code,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'position' => $request->position,
                'salary_type' => $request->salary_type,
                'salary_rate' => $salaryRate,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
            ]);

            DB::commit();

            Log::info('Created new employee: ' . json_encode($employee));

            return redirect()->route('admin.employees.index')
                ->with('success', 'Thêm nhân viên thành công. Mật khẩu mặc định là: bia123');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating employee: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])
                ->withInput();
        }
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
            'email' => 'required|email|unique:users,email,' . ($employee->user_id ?? 'NULL') . '|unique:employees,email,' . $id,
            'address' => 'nullable|string|max:500',
            'position' => 'required|in:manager,staff,cashier,waiter',
            'salary_type' => 'required|in:hourly,monthly',
            'salary_rate' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $employeeRole = Role::where('slug', User::ROLE_EMPLOYEE)->first();
            if (!$employeeRole) {
                throw new \Exception('Role "employee" not found.');
            }

            // Cập nhật user
            if ($employee->user_id) {
                $user = $employee->user;
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'role_id' => $employeeRole->id,
                    'status' => $request->status,
                ]);
            } else {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'role_id' => $employeeRole->id,
                    'password' => Hash::make('nhanvien'),
                    'status' => $request->status,
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

            DB::commit();

            return redirect()->route('admin.employees.index')
                ->with('success', 'Nhân viên đã được cập nhật thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating employee: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $employee = Employee::findOrFail($id);

            if ($employee->user) {
                $employee->user->delete();
            }

            $employee->delete();

            DB::commit();

            Log::info('Deleted employee: ' . json_encode($employee));

            return redirect()->route('admin.employees.index')
                ->with('success', 'Nhân viên đã được xóa thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting employee: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa nhân viên.');
        }
    }
    public function updateSalary(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'salary_type' => 'required|in:hourly,monthly',
            'salary_rate' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $employee = Employee::findOrFail($id);
            
            $employee->update([
                'salary_type' => $request->salary_type,
                'salary_rate' => $request->salary_rate
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật lương thành công',
                'data' => [
                    'salary_type' => $employee->salary_type,
                    'salary_rate' => $employee->salary_rate
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating salary: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi cập nhật lương'
            ], 500);
        }
    }
}
