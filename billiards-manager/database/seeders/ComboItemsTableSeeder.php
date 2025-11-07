<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Combo;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComboItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy combo
        $combo1 = Combo::where('combo_code', 'COMBO001')->firstOrFail();
        $combo2 = Combo::where('combo_code', 'COMBO002')->firstOrFail();

        // Lấy category bàn (type = table)
        $standardCat = Category::where('name', 'Khu vực Standard')->firstOrFail();
        $vipCat = Category::where('name', 'Khu vực VIP')->firstOrFail();

        // Lấy sản phẩm tiêu thụ (còn tồn tại trong bảng products)
        $coke = Product::where('product_code', 'DRK001')->firstOrFail();
        $pepsi = Product::where('product_code', 'DRK002')->firstOrFail();
        $bim = Product::where('product_code', 'FOD001')->firstOrFail();

        DB::table('combo_items')->insert([
            // === COMBO001: 2h Standard + 2 nước (chọn 2 trong 2) ===
            [
                'combo_id' => $combo1->id,
                'product_id' => null,
                'quantity' => 2,
                'unit_price' => null,
                'table_category_id' => $standardCat->id,
                'table_price_per_hour' => 50000,
                'is_required' => true,
                'choice_group' => null,
                'max_choices' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'combo_id' => $combo1->id,
                'product_id' => $coke->id,
                'quantity' => 1,
                'unit_price' => 20000,
                'table_category_id' => null,
                'table_price_per_hour' => null,
                'is_required' => false,
                'choice_group' => 'drinks',
                'max_choices' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'combo_id' => $combo1->id,
                'product_id' => $pepsi->id,
                'quantity' => 1,
                'unit_price' => 20000,
                'table_category_id' => null,
                'table_price_per_hour' => null,
                'is_required' => false,
                'choice_group' => 'drinks',
                'max_choices' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // === COMBO002: 3h VIP + 1 snack ===
            [
                'combo_id' => $combo2->id,
                'product_id' => null,
                'quantity' => 3,
                'unit_price' => null,
                'table_category_id' => $vipCat->id,
                'table_price_per_hour' => 80000,
                'is_required' => true,
                'choice_group' => null,
                'max_choices' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'combo_id' => $combo2->id,
                'product_id' => $bim->id,
                'quantity' => 1,
                'unit_price' => 15000,
                'table_category_id' => null,
                'table_price_per_hour' => null,
                'is_required' => true,
                'choice_group' => null,
                'max_choices' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
