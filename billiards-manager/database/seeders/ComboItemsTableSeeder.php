<?php

namespace Database\Seeders;

use App\Models\Combo;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComboItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        $combo1 = Combo::where('combo_code', 'COMBO001')->firstOrFail();
        $combo2 = Combo::where('combo_code', 'COMBO002')->firstOrFail();

        $serviceStd = Product::where('product_code', 'SERVICE001')->firstOrFail();
        $serviceVip = Product::where('product_code', 'SERVICE002')->firstOrFail();
        $coke = Product::where('product_code', 'DRK001')->firstOrFail();
        $pepsi = Product::where('product_code', 'DRK002')->firstOrFail();
        $bim = Product::where('product_code', 'FOD001')->firstOrFail();

        DB::table('combo_items')->insert([
            // COMBO001: 2h + 2 nước
            [
                'combo_id' => $combo1->id,
                'product_id' => $serviceStd->id,
                'quantity' => 2,
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
                'is_required' => false,
                'choice_group' => 'drinks',
                'max_choices' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // COMBO002: 3h VIP + snack
            [
                'combo_id' => $combo2->id,
                'product_id' => $serviceVip->id,
                'quantity' => 3,
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
                'is_required' => true,
                'choice_group' => null,
                'max_choices' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
