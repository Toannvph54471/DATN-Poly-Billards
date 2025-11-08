<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\TableRate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class TableRatesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('table_rates')->truncate();
        $catVip = Category::where('type', 'table')->where('name', 'Bàn VIP')->first();

        TableRate::create([
            'code' => 'VIP_WEEKDAY',
            'name' => 'Giờ ngày thường (VIP)',
            'category_id' => $catVip->id,
            'hourly_rate' => 100000,
        ]);
        
        TableRate::create([
            'code' => 'VIP_WEEKEND',
            'name' => 'Giờ cuối tuần (VIP)',
            'category_id' => $catVip->id,
            'hourly_rate' => 120000,
        ]);
    }
}
