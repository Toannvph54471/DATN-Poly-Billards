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
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $id,
        'phone' => 'required|string|max:15',
        'role_id' => 'required|exists:roles,id',
        'status' => 'required|in:Active,Inactive',
    ]);

    $user = User::findOrFail($id);
    $oldRoleSlug = $user->role ? $user->role->slug : null;
    $newRole = Role::findOrFail($request->role_id);
    $newRoleSlug = $newRole->slug;

    // Lấy vai trò hiện tại để giữ nguyên nếu là admin hoặc manager
    $currentRoleId = $user->role_id;

    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'role_id' => $newRoleSlug === User::ROLE_EMPLOYEE ? $request->role_id : $currentRoleId, // Giữ nguyên vai trò nếu không phải employee
        'status' => $request->status,
    ]);

    // Đồng bộ với Employee chỉ khi vai trò là employee
    if ($newRoleSlug === User::ROLE_EMPLOYEE) {
        if (!$user->employee) {
            Employee::create([
                'user_id' => $user->id,
                'employee_code' => 'EMP-' . Str::random(6),
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'position' => 'staff',
                'salary_type' => 'hourly',
                'salary_rate' => 25000.00,
                'start_date' => now(),
                'status' => $request->status === 'Active' ? 0 : 1,
            ]);
        } else {
            $user->employee->update([
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'status' => $request->status === 'Active' ? 0 : 1,
            ]);
        }
    } elseif ($oldRoleSlug === User::ROLE_EMPLOYEE && $newRoleSlug !== User::ROLE_EMPLOYEE) {
        if ($user->employee) {
            $user->employee->delete();
        }
    }

        return redirect()->route('admin.users.edit', $user->id)
            ->with('success', 'Cập nhật thông tin thành viên thành công!');
        }
    }