<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PromotionProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('promotion_products')->truncate();
       // Gán khuyến mãi cho sản phẩm cụ thể (nếu cần)
        // $promo = Promotion::first();
        // $product = Product::first();
        // if ($promo && $product) {
        //     DB::table('promotion_products')->insert([
        //         'promotion_id' => $promo->id,
        //         'product_id' => $product->id
        //     ]);
        // }
    }
}
