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
        DB::table('employees')->insert([
            [
                'user_id' => User::where('email', 'manager@bida.com')->first()->id,
                'position' => 'Manager',
                'salary_rate' => 35000, // 35,000đ/giờ
                'hire_date' => '2024-01-15',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => User::where('email', 'staff@bida.com')->first()->id,
                'position' => 'Employee',
                'salary_rate' => 25000, // 25,000đ/giờ
                'hire_date' => '2024-02-01',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}