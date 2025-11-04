<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'name' => 'Quản trị viên',
                'slug' => 'admin',
                'description' => 'Toàn quyền hệ thống',
                'permissions' => json_encode(config('permissions.role_permissions.admin'))
            ],
            [
                'name' => 'Quản lý',
                'slug' => 'manager',
                'description' => 'Quản lý cửa hàng',
                'permissions' => json_encode(config('permissions.role_permissions.manager'))
            ],
            [
                'name' => 'Nhân viên',
                'slug' => 'employee',
                'description' => 'Nhân viên phục vụ',
                'permissions' => json_encode(config('permissions.role_permissions.employee'))
            ],
                        [
                'name' => 'Khách hàng',
                'slug' => 'customer',
                'description' => 'Tài khoản khách hàng',
                'permissions' => json_encode([
                    'reservation.view_own',
                    'reservation.create',
                    'reservation.cancel_own',
                    'profile.view',
                    'profile.update'
                ])
            ],

        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}