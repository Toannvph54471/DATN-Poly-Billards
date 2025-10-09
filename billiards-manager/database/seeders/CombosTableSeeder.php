<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CombosTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('combos')->insert([
            [
                'name' => 'Combo 2h + 2 nước',
                'description' => 'Bao gồm 2 giờ chơi và 2 nước ngọt',
                'price' => 140000,
                'actual_value' => 160000, // 2*60k + 2*20k
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Combo 3h VIP + snack',
                'description' => 'Bao gồm 3 giờ chơi VIP và 1 snack',
                'price' => 250000,
                'actual_value' => 280000, // 3*80k + 40k
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}