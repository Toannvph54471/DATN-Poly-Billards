<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\Payroll;
use App\Models\User;
use App\Models\Role;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with([
            'user.role'
        ]);

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
        $roles = Role::whereIn('id', [2, 3])->get();
        return view('admin.employees.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_code' => 'required|unique:employees,employee_code',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:employees,phone|unique:users,phone',
            'email' => 'required|email|unique:users,email|unique:employees,email',
            'address' => 'nullable|string|max:500',

            'role_id' => 'required|exists:roles,id',
            'hourly_rate' => 'required|numeric|min:0',

            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Bắt đầu transaction
        DB::beginTransaction();

        // ✔ Tạo User
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'role_id'   => $request->role_id,
            'password'  => Hash::make('12345678'), // mật khẩu mặc định
            'status'    => $request->status,
        ]);

        // ✔ Tạo Employee
        $employee = Employee::create([
            'user_id'       => $user->id,
            'employee_code' => $request->employee_code,
            'name'          => $request->name,
            'phone'         => $request->phone,
            'email'         => $request->email,
            'address'       => $request->address,
            'position'      => $request->role_id,
            'hourly_rate'   => $request->hourly_rate,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'status'        => $request->status,
        ]);

        // Auto-generate QR Token for immediate use
        $employee->generateQrToken();

        // Commit thành công
        DB::commit();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Thêm nhân viên thành công. Mật khẩu mặc định là: 12345678');
    }


    public function show($id)
    {
        $employee = Employee::with('employeeShifts.shift')->findOrFail($id);
        return view('admin.employees.show', compact('employee'));
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $roles = Role::whereIn('id', [2, 3])->get();

        return view('admin.employees.edit', compact('employee', 'roles'));
    }


    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_code' => 'required|unique:employees,employee_code,' . $id,
            'name'          => 'required|string|max:255',

            'phone' => 'required|string|max:15|unique:employees,phone,' . $id .
                '|unique:users,phone,' . ($employee->user_id ?? 'NULL'),

            'email' => 'required|email|unique:employees,email,' . $id .
                '|unique:users,email,' . ($employee->user_id ?? 'NULL'),

            'address'   => 'nullable|string|max:500',

            'role_id'   => 'required|exists:roles,id',  // dùng role_id từ select
            'hourly_rate' => 'required|numeric|min:0',

            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',

            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        // ==== Cập nhật User ====
        if ($employee->user_id) {
            // Update user đã tồn tại
            $user = $employee->user;

            $user->update([
                'name'    => $request->name,
                'email'   => $request->email,
                'phone'   => $request->phone,
                'role_id' => $request->role_id,
                'status'  => $request->status,
            ]);
        } else {
            // Nếu employee chưa có user → tạo mới
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'role_id'  => $request->role_id,
                'password' => Hash::make('12345678'), // mật khẩu mặc định
                'status'   => $request->status,
            ]);

            $employee->user_id = $user->id;
        }

        // ==== Cập nhật Employee ====
        $employee->update([
            'employee_code' => $request->employee_code,
            'name'          => $request->name,
            'phone'         => $request->phone,
            'email'         => $request->email,
            'address'       => $request->address,

            'position'      => $request->role_id, // nếu muốn lưu role_id vào position
            'hourly_rate'   => $request->hourly_rate,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'status'        => $request->status,
            'user_id'       => $employee->user_id,
        ]);

        DB::commit();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Cập nhật nhân viên thành công.');
    }   

    // File: app/Http/Controllers/EmployeeController.php

public function updateHourlyRate(Request $request, $id)
    {
        // Chỉ admin/manager mới được phép
        if (!Auth::user()->hasRole(['admin', 'manager'])) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403);
        }
    $request->validate([
        'hourly_rate' => 'required|numeric|min:0',
    ]);

    $employee = \App\Models\Employee::findOrFail($id);

    $employee->update([
        'hourly_rate' => $request->hourly_rate
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Cập nhật lương giờ thành công!',
        'hourly_rate' => number_format($employee->hourly_rate, 0, ',', '.')
    ]);
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
            'hourly_rate' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $employee = Employee::findOrFail($id);
            
            // Debugging as requested
            // dd([
            //     'request' => $request->all(),
            //     'employee_attributes' => $employee->getAttributes(),
            //     'fillable' => $employee->getFillable()
            // ]);

            // Explicitly set hourly_rate
            $employee->hourly_rate = $request->hourly_rate;
            $employee->save();

            Log::info("Updated hourly_rate for employee {$id} to {$request->hourly_rate}");

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật lương thành công',
                'data' => [
                    'hourly_rate' => $employee->hourly_rate
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

    public function myProfile()
    {
        try {
            // Lấy user đang đăng nhập
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
            }

            // Lấy thông tin employee từ user
            $employee = $user->employee;

            if (!$employee) {
                return redirect()->route('admin.pos.dashboard')
                    ->with('error', 'Không tìm thấy thông tin nhân viên!');
            }

            return view('admin.employees.my-profile', compact(
                'user',
                'employee'
            ));
        } catch (\Exception $e) {
            Log::error('Lỗi myProfile: ' . $e->getMessage());

            return redirect()->route('admin.pos.dashboard')
                ->with('error', 'Có lỗi xảy ra khi tải thông tin!');
        }
    }

    public function changePassword(Request $request, $id)
    {
        try {
            // Kiểm tra employee có tồn tại
            $employee = Employee::findOrFail($id);

            // Kiểm tra quyền: chỉ employee đó mới được đổi mật khẩu của mình
            $currentUser = Auth::user();
            if ($currentUser->employee->id != $employee->id && !$currentUser->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện thao tác này!'
                ], 403);
            }

            // Validate dữ liệu
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|min:6|confirmed',
            ], [
                'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
                'new_password.required' => 'Vui lòng nhập mật khẩu mới',
                'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự',
                'new_password.confirmed' => 'Xác nhận mật khẩu không khớp',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            // Kiểm tra mật khẩu hiện tại
            if (!Hash::check($request->current_password, $currentUser->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu hiện tại không đúng!'
                ]);
            }

            // Đổi mật khẩu
            $currentUser->password = Hash::make($request->new_password);
            $currentUser->save();

            return response()->json([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công!'
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi changePassword: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    public function mySchedule()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login');
            }

            $employee = $user->employee;

            if (!$employee) {
                return redirect()->route('admin.pos.dashboard')
                    ->with('error', 'Không tìm thấy thông tin nhân viên!');
            }

            // Lấy tháng và năm từ request
            $month = request()->input('month', now()->month);
            $year = request()->input('year', now()->year);

            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            // Lấy TẤT CẢ ca làm của nhân viên trong tháng
            $shifts = EmployeeShift::where('employee_id', $employee->id)
                ->whereBetween('shift_date', [$startDate, $endDate])
                ->with(['shift'])
                ->orderBy('shift_date', 'asc')
                ->paginate(15); // Phân trang 15 ca/trang

            // Phân loại ca làm - chỉ lấy 5 ca sắp tới
            $todayShifts = EmployeeShift::where('employee_id', $employee->id)
                ->whereDate('shift_date', today())
                ->with(['shift'])
                ->get();

            $upcomingShifts = EmployeeShift::where('employee_id', $employee->id)
                ->where('shift_date', '>', today()->format('Y-m-d'))
                ->where('status', 'scheduled')
                ->with(['shift'])
                ->orderBy('shift_date', 'asc')
                ->take(5) // Chỉ lấy 5 ca sắp tới
                ->get();

            $completedShifts = EmployeeShift::where('employee_id', $employee->id)
                ->where('status', 'completed')
                ->whereBetween('shift_date', [$startDate, $endDate])
                ->count();

            // Tổng hợp thống kê
            $stats = [
                'total' => $shifts->total(),
                'completed' => $completedShifts,
                'upcoming' => $upcomingShifts->count(),
                'today' => $todayShifts->count(),
            ];

            return view('admin.employees.schedule', compact(
                'user',
                'employee',
                'shifts', // Đã phân trang
                'todayShifts',
                'upcomingShifts',
                'stats',
                'month',
                'year',
                'startDate',
                'endDate'
            ));
        } catch (\Exception $e) {
            \Log::error('Lỗi mySchedule: ' . $e->getMessage());

            return redirect()->route('admin.pos.dashboard')
                ->with('error', 'Có lỗi xảy ra khi tải lịch làm việc!');
        }
    }
}
