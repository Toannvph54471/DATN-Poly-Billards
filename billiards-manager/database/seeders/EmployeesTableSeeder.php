<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('employees')->insert([
            [
                'user_id' => 2, // Quản Lý A
                'position' => 'Manager',
                'salary_rate' => 35000, // 35,000đ/giờ
                'hire_date' => '2024-01-15',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => 3, // Nhân Viên B
                'position' => 'Staff',
                'salary_rate' => 25000, // 25,000đ/giờ
                'hire_date' => '2024-02-01',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}