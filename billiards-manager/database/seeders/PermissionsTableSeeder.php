<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('permissions')->truncate();
        
        $modules = ['User', 'Role', 'Product', 'Table', 'Reservation', 'Bill', 'Report'];
        $actions = ['Create', 'View', 'Update', 'Delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::create([
                    'name' => $action . ' ' . $module,
                    'module' => $module
                ]);
            }
        }
    }
}