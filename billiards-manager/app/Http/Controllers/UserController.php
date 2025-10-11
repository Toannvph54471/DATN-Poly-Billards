<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

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
        $staffRoleId = Role::where('slug', 'staff')->value('id');
        $managerRoleId = Role::where('slug', 'manager')->value('id');

        $adminCount = $adminRoleId ? User::where('role_id', $adminRoleId)->count() : 0;
        $staffCount = $staffRoleId ? User::where('role_id', $staffRoleId)->count() : 0;
        $managerCount = $managerRoleId ? User::where('role_id', $managerRoleId)->count() : 0;

        // --- Lấy danh sách người dùng ---
        $listUser = $users->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.users.index', compact(
            'listUser',
            'totalUser',
            'adminCount',
            'staffCount',
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
            'role_id' => 'required|exists:roles,id', // kiểm tra role_id tồn tại trong bảng roles
            'status' => 'required|in:Active,Inactive'
        ]);
        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);
        // var_dump($user['role_id']);die;
        return redirect()->route('admin.users.edit', $user->id)
            ->with('success', 'Cập nhật thông tin thành viên thành công!');
    }
}
