<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;

            $users->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $status = $request->status;
            $users->where('status', $status);
        }
        if ($request->has('role') && !empty($request->role)) {
            $users->where('role', $request->role);
        }
        $totalUser = User::count();

        $listUser = $users->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.users.index', compact('listUser', 'totalUser'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        // var_dump($id);die;
        // Validation cơ bản
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'required|string|max:15',
            'role' => 'required|in:admin,member,employees',
            'status' => 'required|in:active,inactive'
        ]);

        // Cập nhật user
        $user = User::findOrFail($id);
        $user->update($request->all());

        return redirect()->route('admin.users.edit', $user->id)
            ->with('success', 'Cập nhật thông tin thành viên thành công!');
    }
}
