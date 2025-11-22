<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Combo;
use App\Models\Category;
class CombosTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('combos')->truncate();
        Combo::create([
            'combo_code' => 'C_TIME_2H', 
            'name' => 'Combo 2 giờ chơi + 2 nước', 
            'price' => 150000,
            'is_time_combo' => true,
            'play_duration_minutes' => 120,
        ]);
        
        Combo::create([
            'combo_code' => 'C_FOOD_SNACK', 
            'name' => 'Combo Đồ ăn vặt', 
            'price' => 100000,
            'is_time_combo' => false,
        ]);
    }
}
