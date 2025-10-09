<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin System',
                'email' => 'admin@bida.com',
                'phone' => '0909111111',
                'role_id' => 1, // Admin
                'password' => Hash::make('123456'),
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Quản Lý A',
                'email' => 'manager@bida.com',
                'phone' => '0909222222',
                'role_id' => 2, // Manager
                'password' => Hash::make('123456'),
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Nhân Viên B',
                'email' => 'staff@bida.com',
                'phone' => '0909333333',
                'role_id' => 3, // Staff
                'password' => Hash::make('123456'),
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}