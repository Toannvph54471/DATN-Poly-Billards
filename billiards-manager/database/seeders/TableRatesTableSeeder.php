<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\TableRate;
use Illuminate\Database\Seeder;

class TableRatesTableSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            [
                'code' => 'SERVICE001',
                'name' => 'Giờ chơi bàn Standard',
                'category_id' => Category::where('name', 'Khu vực Standard')->first()->id,
                'hourly_rate' => 50000,
                'status' => 'Active',
            ],
            [
                'code' => 'SERVICE002',
                'name' => 'Giờ chơi bàn VIP',
                'category_id' => Category::where('name', 'Khu vực VIP')->first()->id,
                'hourly_rate' => 80000,
                'status' => 'Active',
            ],
            [
                'code' => 'SERVICE003',
                'name' => 'Giờ chơi bàn Competition',
                'category_id' => Category::where('name', 'Khu vực Thi đấu')->first()->id,
                'hourly_rate' => 100000,
                'status' => 'Active',
            ],
        ];

        foreach ($rates as $rate) {
            TableRate::create($rate);
        }
    }
}
