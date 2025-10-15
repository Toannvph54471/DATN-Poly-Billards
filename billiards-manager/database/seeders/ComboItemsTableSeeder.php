<?php

namespace Database\Seeders;

use App\Models\Combo;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComboItemsTableSeeder extends Seeder
{
    public function run()
    {
        // Combo 2h + 2 nước
        DB::table('combo_items')->insert([
            [
                'combo_id' => Combo::where('combo_code', 'COMBO001')->first()->id,
                'product_id' => Product::where('product_code', 'SERVICE001')->first()->id,
                'quantity' => 2,
                'is_required' => true,
                'choice_group' => null,
                'max_choices' => null,
                'created_at' => now()
            ],
            [
                'combo_id' => Combo::where('combo_code', 'COMBO001')->first()->id,
                'product_id' => Product::where('product_code', 'DRK001')->first()->id,
                'quantity' => 2,
                'is_required' => false,
                'choice_group' => 'drinks',
                'max_choices' => 2,
                'created_at' => now()
            ],
            [
                'combo_id' => Combo::where('combo_code', 'COMBO001')->first()->id,
                'product_id' => Product::where('product_code', 'DRK002')->first()->id,
                'quantity' => 1,
                'is_required' => false,
                'choice_group' => 'drinks',
                'max_choices' => 2,
                'created_at' => now()
            ],

        ]);

        // Combo 3h VIP + snack
        DB::table('combo_items')->insert([
             [
                'combo_id' => Combo::where('combo_code', 'COMBO002')->first()->id,
                'product_id' => Product::where('product_code', 'SERVICE002')->first()->id,
                'quantity' => 2,
                'is_required' => true,
                'choice_group' => null,
                'max_choices' => null,
                'created_at' => now()
            ],
            [
                'combo_id' => Combo::where('combo_code', 'COMBO002')->first()->id,
                'product_id' => Product::where('product_code', 'FOD001')->first()->id,
                'quantity' => 2,
                'is_required' => false,
                'choice_group' => 'drinks',
                'max_choices' => 2,
                'created_at' => now()
            ],
        ]);
    }
}