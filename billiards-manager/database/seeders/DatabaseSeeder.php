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
            CategoriesTableSeeder::class,
            ShiftsTableSeeder::class,
            EmployeesTableSeeder::class,
            TablesTableSeeder::class,
            ProductsTableSeeder::class,
            CombosTableSeeder::class,
            ComboItemsTableSeeder::class,
            PromotionSeeder::class,
            PromotionProductsSeeder::class,
            PromotionComboSeeder::class,
        ]);
    }
}
