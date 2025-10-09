<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComboItemsTableSeeder extends Seeder
{
    public function run()
    {
        // Combo 2h + 2 nước
        DB::table('combo_items')->insert([
            [
                'combo_id' => 1,
                'product_id' => 7, // Giờ chơi Regular
                'quantity' => 2,
                'is_required' => true,
                'choice_group' => null,
                'max_choices' => null,
                'created_at' => now()
            ],
            [
                'combo_id' => 1,
                'product_id' => 1, // Coca-Cola
                'quantity' => 2,
                'is_required' => false,
                'choice_group' => 'drinks',
                'max_choices' => 2,
                'created_at' => now()
            ],
            [
                'combo_id' => 1,
                'product_id' => 2, // Pepsi
                'quantity' => 1,
                'is_required' => false,
                'choice_group' => 'drinks',
                'max_choices' => 2,
                'created_at' => now()
            ],
            [
                'combo_id' => 1,
                'product_id' => 3, // Sting
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
                'combo_id' => 2,
                'product_id' => 8, // Giờ chơi VIP
                'quantity' => 3,
                'is_required' => true,
                'choice_group' => null,
                'max_choices' => null,
                'created_at' => now()
            ],
            [
                'combo_id' => 2,
                'product_id' => 5, // Bắp rang bơ
                'quantity' => 1,
                'is_required' => false,
                'choice_group' => 'snacks',
                'max_choices' => 1,
                'created_at' => now()
            ],
            [
                'combo_id' => 2,
                'product_id' => 6, // Khoai tây chiên
                'quantity' => 1,
                'is_required' => false,
                'choice_group' => 'snacks',
                'max_choices' => 1,
                'created_at' => now()
            ],
        ]);
    }
}