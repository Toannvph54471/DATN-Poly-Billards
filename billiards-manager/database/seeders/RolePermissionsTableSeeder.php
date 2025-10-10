<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionsTableSeeder extends Seeder
{
    public function run()
    {
        // Admin - Tất cả permissions
        $adminPermissions = DB::table('permissions')->pluck('id');
        foreach ($adminPermissions as $permissionId) {
            DB::table('role_permissions')->insert([
                'role_id' => 1, // Admin
                'permission_id' => $permissionId,
                'created_at' => now()
            ]);
        }

        // Manager - Hầu hết permissions, trừ delete_users
        $managerPermissions = DB::table('permissions')
            ->whereNotIn('name', ['delete_users'])
            ->pluck('id');
        
        foreach ($managerPermissions as $permissionId) {
            DB::table('role_permissions')->insert([
                'role_id' => 2, // Manager
                'permission_id' => $permissionId,
                'created_at' => now()
            ]);
        }

        // Staff - Chỉ permissions cơ bản
        $staffPermissions = DB::table('permissions')
            ->whereIn('name', [
                'view_tables', 
                'open_bills',
                'add_order_items',
                'view_products',
                'view_reservations',
                'create_reservations',
                'confirm_checkin'
            ])
            ->pluck('id');
        
        foreach ($staffPermissions as $permissionId) {
            DB::table('role_permissions')->insert([
                'role_id' => 3, // Staff
                'permission_id' => $permissionId,
                'created_at' => now()
            ]);
        }
    }
}