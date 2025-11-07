<?php

namespace Database\Seeders;

use App\Models\Combo;
use Illuminate\Database\Seeder;

class CombosTableSeeder extends Seeder
{
    public function run(): void
    {
        $combos = [
            [
                'combo_code' => 'COMBO001',
                'name' => 'Combo 2h + 2 nước',
                'description' => 'Bao gồm 2 giờ chơi và 2 nước ngọt',
                'price' => 140000,
                'actual_value' => 160000,
                'status' => 'Active',
            ],
            [
                'combo_code' => 'COMBO002',
                'name' => 'Combo 3h VIP + snack',
                'description' => 'Bao gồm 3 giờ chơi VIP và 1 snack',
                'price' => 250000,
                'actual_value' => 280000,
                'status' => 'Active',
            ],
        ];

        foreach ($combos as $combo) {
            Combo::firstOrCreate(
                ['combo_code' => $combo['combo_code']], 
                $combo
            );
        }
    }
}
