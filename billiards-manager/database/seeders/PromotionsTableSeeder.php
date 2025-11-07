<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Promotion;

class PromotionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('promotions')->truncate();
        
        Promotion::create([
            'promotion_code' => 'WELCOME10',
            'name' => 'Giảm 10% cho khách hàng mới',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
        ]);
    }
}
