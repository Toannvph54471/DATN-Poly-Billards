<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
       DB::table('roles')->truncate(); // Xóa dữ liệu cũ
        
        Role::create([
            'name' => 'Admin', 
            'slug' => 'admin', 
            'description' => 'Quản trị viên cao nhất'
        ]);
        
        Role::create([
            'name' => 'Manager', 
            'slug' => 'manager', 
            'description' => 'Quản lý vận hành'
        ]);
        
        Role::create([
            'name' => 'Employee', 
            'slug' => 'employee', 
            'description' => 'Nhân viên (thu ngân, phục vụ)'
        ]);
        
        Role::create([
            'name' => 'Customer', 
            'slug' => 'customer', 
            'description' => 'Khách hàng (đặt bàn, thành viên)'
        ]);
    }
}