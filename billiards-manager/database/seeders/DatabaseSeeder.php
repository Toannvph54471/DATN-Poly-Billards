<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $this->call([
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            CategoriesTableSeeder::class,
            TableRatesTableSeeder::class,
            TablesTableSeeder::class,
            ProductsTableSeeder::class,
            ShiftsTableSeeder::class,
            CombosTableSeeder::class,
            ComboItemsTableSeeder::class,
            PromotionsTableSeeder::class
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}