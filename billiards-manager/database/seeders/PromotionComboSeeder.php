<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PromotionComboSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('promotion_combo')->insert([
            [
                'promotion_id' => 1,
                'combo_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'promotion_id' => 1,
                'combo_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'promotion_id' => 3,
                'combo_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
