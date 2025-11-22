<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('role_permissions')->truncate();

        // Admin: Gán tất cả quyền
        $adminRole = Role::where('slug', 'admin')->first();
        $allPermissions = Permission::pluck('id');
        $adminRole->permissions()->attach($allPermissions);

        // Manager: Gán quyền trừ quản lý User/Role
        $managerRole = Role::where('slug', 'manager')->first();
        $managerPermissions = Permission::whereNotIn('module', ['User', 'Role'])->pluck('id');
        $managerRole->permissions()->attach($managerPermissions);
        
        // (Bạn có thể gán quyền chi tiết hơn cho Employee và Customer ở đây)
    }
}