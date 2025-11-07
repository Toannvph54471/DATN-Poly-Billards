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
       DB::table('employees')->truncate();

        // Lấy user Employee đã tạo
        $employeeUser = User::where('email', 'employee@polybilliards.com')->first();

        if ($employeeUser) {
            Employee::create([
                'user_id' => $employeeUser->id,
                'employee_code' => 'EMP001',
                'name' => $employeeUser->name,
                'phone' => $employeeUser->phone,
                'email' => $employeeUser->email,
                'position' => 'Staff',
                'salary_type' => 'hourly',
                'salary_rate' => 30000,
                'start_date' => now()->subMonth(),
            ]);
        }
    }
}