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
                'combo_code' => 'COMBO001',
                'name' => 'Combo 2h + 2 nước',
                'description' => 'Bao gồm 2 giờ chơi và 2 nước ngọt',
                'price' => 140000,
                'actual_value' => 160000,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'combo_code' => 'COMBO002',
                'name' => 'Combo 3h VIP + snack',
                'description' => 'Bao gồm 3 giờ chơi VIP và 1 snack',
                'price' => 250000,
                'actual_value' => 280000,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
        ]);
    }
}
