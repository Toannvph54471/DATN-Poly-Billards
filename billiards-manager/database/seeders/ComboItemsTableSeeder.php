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
        DB::table('combo_items')->truncate();
        
        $comboTime = Combo::where('combo_code', 'C_TIME_2H')->first();
        $coca = Product::where('product_code', 'DR001')->first();
        
        // Thêm 2 lon Coca vào Combo 2 giờ
        if ($comboTime && $coca) {
            DB::table('combo_items')->insert([
                'combo_id' => $comboTime->id,
                'product_id' => $coca->id,
                'quantity' => 2,
                'unit_price' => $coca->price,
            ]);
        }
    }
}
