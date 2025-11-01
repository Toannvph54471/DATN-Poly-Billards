<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Cho Products
            ['name' => 'Dịch vụ', 'type' => 'product', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Nước ngọt', 'type' => 'product', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Snack', 'type' => 'product', 'created_at' => now(), 'updated_at' => now()],

            // Cho Tables
            ['name' => 'Khu vực Standard', 'type' => 'table', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Khu vực VIP', 'type' => 'table', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Khu vực Thi đấu', 'type' => 'table', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categories')->insert($categories);
    }
}
