<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Hiển thị form đăng ký.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Xử lý request đăng ký.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:15', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        // Tìm role_id từ bảng roles dựa trên slug
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
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        // Tạo employee tương ứng
        Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'position' => 'staff',
            'salary_type' => 'hourly',
            'salary_rate' => 25000.00,
            'start_date' => now(),
            'status' => 'Active',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect('/')->with('success', 'Đăng ký thành công!');
    }
}