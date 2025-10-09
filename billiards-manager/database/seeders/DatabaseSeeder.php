<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesTableSeeder::class,
            PermissionsTableSeeder::class,
            RolePermissionsTableSeeder::class,
            UsersTableSeeder::class,
            EmployeesTableSeeder::class,
            ShiftsTableSeeder::class,
            TablesTableSeeder::class,
            ProductsTableSeeder::class,
            CombosTableSeeder::class,
            ComboItemsTableSeeder::class,
        ]);
    }
}