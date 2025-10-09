<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class StaffController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user.role')->get();
        return view('staff.index', compact('employees'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'position' => 'required|in:Staff,Manager',
            'salary_rate' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'password' => 'required|min:6'
        ]);

        // Tạo user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->position === 'Manager' ? 2 : 3,
            'password' => Hash::make($request->password),
            'status' => 'Active'
        ]);

        // Tạo employee
        Employee::create([
            'user_id' => $user->id,
            'position' => $request->position,
            'salary_rate' => $request->salary_rate,
            'hire_date' => $request->hire_date,
            'status' => 'Active'
        ]);

        return redirect()->route('staff.index')->with('success', 'Tạo nhân viên thành công!');
    }

    public function show(Employee $employee)
    {
        $employee->load(['user', 'shifts.shift', 'attendance']);
        return view('staff.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $employee->load('user');
        return view('staff.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'phone' => 'required|string|max:20',
            'position' => 'required|in:Staff,Manager',
            'salary_rate' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive'
        ]);

        // Update user
        $employee->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->position === 'Manager' ? 2 : 3,
        ]);

        // Update employee
        $employee->update([
            'position' => $request->position,
            'salary_rate' => $request->salary_rate,
            'status' => $request->status
        ]);

        return redirect()->route('staff.index')->with('success', 'Cập nhật nhân viên thành công!');
    }

    public function destroy(Employee $employee)
    {
        $employee->user->update(['status' => 'Inactive']);
        $employee->update(['status' => 'Inactive']);

        return redirect()->route('staff.index')->with('success', 'Vô hiệu hóa nhân viên thành công!');
    }

    public function shifts(Request $request)
    {
        $query = EmployeeShift::with(['employee.user', 'shift', 'confirmedBy']);

        if ($request->has('date')) {
            $query->whereDate('shift_date', $request->date);
        }

        $shifts = $query->orderBy('shift_date', 'desc')->paginate(20);
        $employees = Employee::where('status', 'Active')->get();
        $shiftTypes = Shift::all();

        return view('staff.shifts', compact('shifts', 'employees', 'shiftTypes'));
    }

    public function assignShift(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'shift_date' => 'required|date'
        ]);

        EmployeeShift::create([
            'employee_id' => $request->employee_id,
            'shift_id' => $request->shift_id,
            'shift_date' => $request->shift_date,
            'status' => 'Scheduled'
        ]);

        return redirect()->back()->with('success', 'Phân ca thành công!');
    }

    public function checkin(Request $request, EmployeeShift $employeeShift)
    {
        if ($employeeShift->status !== 'Scheduled') {
            return redirect()->back()->with('error', 'Không thể check-in cho ca này!');
        }

        $employeeShift->update([
            'actual_start_time' => now(),
            'status' => 'Working',
            'confirmed_by' => $request->user()->id
        ]);

        // Tạo attendance record
        Attendance::create([
            'employee_id' => $employeeShift->employee_id,
            'check_in' => now(),
            'status' => 'Present',
            'confirmed_by' => $request->user()->id
        ]);

        return redirect()->back()->with('success', 'Check-in thành công!');
    }

    public function checkout(Request $request, EmployeeShift $employeeShift)
    {
        if ($employeeShift->status !== 'Working') {
            return redirect()->back()->with('error', 'Nhân viên chưa check-in!');
        }

        $employeeShift->update([
            'actual_end_time' => now(),
            'status' => 'Completed'
        ]);

        // Update attendance record
        $attendance = Attendance::where('employee_id', $employeeShift->employee_id)
            ->whereDate('check_in', $employeeShift->shift_date)
            ->latest()
            ->first();

        if ($attendance) {
            $attendance->update([
                'check_out' => now()
            ]);
        }

        return redirect()->back()->with('success', 'Check-out thành công!');
    }
}