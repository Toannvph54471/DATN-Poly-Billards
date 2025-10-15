<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeesTableSeeder extends Seeder
{
    public function run()
    {
        // Dữ liệu mẫu cho Manager
        Employee::create([
            'user_id' => User::where('email', 'manager@bida.com')->first()->id ?? null,
            'employee_code' => 'EMP001',
            'name' => 'Nguyen Van Manager',
            'phone' => '0901234567',
            'email' => 'manager@bida.com',
            'address' => '123 Le Loi, TP.HCM',
            'position' => 'manager',
            'salary_type' => 'monthly', // Lương cứng
            'salary_rate' => 35000.00, // 35,000 VND/giờ
            'start_date' => '2024-01-15',
            'status' => 'active',
            'created_by' => 'admin',
            'updated_by' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Dữ liệu mẫu cho Staff
        Employee::create([
            'user_id' => User::where('email', 'staff@bida.com')->first()->id ?? null,
            'employee_code' => 'EMP002',
            'name' => 'Tran Thi Staff',
            'phone' => '0907654321',
            'email' => 'staff@bida.com',
            'address' => '456 Hai Ba Trung, Ha Noi',
            'position' => 'employee',
            'salary_type' => 'hourly', // Part-time
            'salary_rate' => 25000.00, // 25,000 VND/giờ
            'start_date' => '2024-02-01',
            'status' => 'active',
            'created_by' => 'admin',
            'updated_by' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
