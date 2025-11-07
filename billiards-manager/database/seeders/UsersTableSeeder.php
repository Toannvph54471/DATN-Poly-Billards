<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->truncate();

        $adminRole = Role::where('slug', 'admin')->first();
        $managerRole = Role::where('slug', 'manager')->first();
        $employeeRole = Role::where('slug', 'employee')->first();
        $customerRole = Role::where('slug', 'customer')->first();

        // 1. Tạo Admin
        User::create([
            'name' => 'Admin User',
            'phone' => '0900000001',
            'email' => 'admin@polybilliards.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'status' => 'Active',
        ]);

        // 2. Tạo Manager
        User::create([
            'name' => 'Manager User',
            'phone' => '0900000002',
            'email' => 'manager@polybilliards.com',
            'password' => Hash::make('password'),
            'role_id' => $managerRole->id,
            'status' => 'Active',
        ]);

        // 3. Tạo Employee
        User::create([
            'name' => 'Employee User',
            'phone' => '0900000003',
            'email' => 'employee@polybilliards.com',
            'password' => Hash::make('password'),
            'role_id' => $employeeRole->id,
            'status' => 'Active',
        ]);

        // 4. Tạo Customer (Khách vãng lai mẫu)
        User::create([
            'name' => 'Khách vãng lai 1',
            'phone' => '0912345678',
            'email' => null,
            'password' => null, // Khách vãng lai
            'role_id' => $customerRole->id,
            'status' => 'Active',
            'total_spent' => 150000,
            'total_visits' => 1,
        ]);
        
        // 5. Tạo Customer (Khách có tài khoản mẫu)
        User::create([
            'name' => 'Nguyễn Văn A',
            'phone' => '0987654321',
            'email' => 'nguyenvana@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => $customerRole->id,
            'status' => 'Active',
            'total_spent' => 500000,
            'total_visits' => 3,
            'customer_type' => 'Regular',
        ]);
    }
}