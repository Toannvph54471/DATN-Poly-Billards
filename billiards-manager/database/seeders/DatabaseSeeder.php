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
            CustomerSeeder::class,
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
            ReservationSeeder::class,
            ReservationStatusHistorySeeder::class,
            PromotionApplicationSeeder::class,
            CategoriesTableSeeder::class,
            TableRatesTableSeeder::class,     // Thêm mới
            ProductsTableSeeder::class,
            CombosTableSeeder::class,

        ]);
    }
}
