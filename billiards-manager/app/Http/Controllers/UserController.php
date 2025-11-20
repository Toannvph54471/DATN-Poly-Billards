<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query();
        $roles = Role::all();

        // --- Tìm kiếm theo email hoặc số điện thoại ---
        if ($request->filled('search')) {
            $search = $request->search;
            $users->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // --- Lọc theo trạng thái ---
        if ($request->filled('status')) {
            $users->where('status', $request->status);
        }

        // --- Lọc theo role_id ---
        if ($request->filled('role_id')) {
            $users->where('role_id', $request->role_id);
        }

        // --- Tổng số người dùng ---
        $totalUser = User::count();

        // --- Đếm số người dùng theo vai trò ---
        $adminRoleId = Role::where('slug', 'admin')->value('id');
        $employeeRoleId = Role::where('slug', 'employee')->value('id');
        $managerRoleId = Role::where('slug', 'manager')->value('id');

        $adminCount = $adminRoleId ? User::where('role_id', $adminRoleId)->count() : 0;
        $employeeCount = $employeeRoleId ? User::where('role_id', $employeeRoleId)->count() : 0;
        $managerCount = $managerRoleId ? User::where('role_id', $managerRoleId)->count() : 0;

        // --- Lấy danh sách người dùng ---
        $listUser = $users->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.users.index', compact(
            'listUser',
            'totalUser',
            'adminCount',
            'employeeCount',
            'managerCount',
            'roles'
        ));
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'required|string|max:15',
            'role_id' => 'required|exists:roles,id', // Bắt buộc chọn Role hợp lệ
            'status' => 'required|in:Active,Inactive',
        ]);

        $user = User::findOrFail($id);
        
        // Lấy slug của role cũ và mới để so sánh
        $oldRoleSlug = $user->role ? $user->role->slug : '';
        $newRole = Role::findOrFail($request->role_id);
        $newRoleSlug = $newRole->slug;

        // 2. Cập nhật thông tin User (SỬA QUAN TRỌNG TẠI ĐÂY)
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role_id, // Cho phép lưu role mới mà người dùng chọn
            'status' => $request->status,
        ]);

        // 3. Logic đồng bộ bảng Employees (Giữ nguyên logic hay của bạn)
        // Chú ý: Thay User::ROLE_EMPLOYEE bằng 'employee' nếu model chưa khai báo const
        
        // Trường hợp 1: Chuyển thành Employee (Tạo mới hoặc cập nhật)
        if ($newRoleSlug === 'employee') { 
            if (!$user->employee) {
                // Nếu chưa có thông tin nhân viên -> Tạo mới
                Employee::create([
                    'user_id' => $user->id,
                    'employee_code' => 'EMP-' . Str::random(6),
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'position' => 'staff',
                    'salary_type' => 'hourly', // Mặc định
                    'salary_rate' => 25000.00, // Mặc định
                    'start_date' => now(),
                    'status' => $request->status === 'Active' ? 0 : 1,
                ]);
            } else {
                // Nếu đã có -> Cập nhật thông tin
                $user->employee->update([
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'status' => $request->status === 'Active' ? 0 : 1,
                ]);
            }
        } 
        // Trường hợp 2: Từ Employee chuyển sang role khác (Admin/Manager/Customer) -> Xóa thông tin nhân viên
        elseif ($oldRoleSlug === 'employee' && $newRoleSlug !== 'employee') {
            if ($user->employee) {
                $user->employee->delete();
            }
        }

        return redirect()->route('admin.users.index') // Nên về trang index để thấy danh sách cập nhật
            ->with('success', 'Cập nhật người dùng và phân quyền thành công!');
    }
    }