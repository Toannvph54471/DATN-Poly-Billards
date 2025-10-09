<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->insert([
            [
                'name' => 'Admin', 
                'description' => 'Quản trị hệ thống toàn quyền',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Manager', 
                'description' => 'Quản lý quán - giám sát vận hành',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Staff', 
                'description' => 'Nhân viên phục vụ',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}