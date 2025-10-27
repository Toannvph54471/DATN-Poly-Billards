<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PromotionProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('promotion_products')->insert([
            [
                'promotion_id' => 2, // DRINK20
                'product_id' => 1, // Cà phê sữa
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'promotion_id' => 2,
                'product_id' => 2, // Nước cam
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
