<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TablePricing;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class TablePricingTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('table_pricing')->truncate();
        $catStd = Category::where('type', 'table')->where('name', 'Bàn Tiêu Chuẩn')->first();

        TablePricing::create([
            'category_id' => $catStd->id,
            'duration_minutes' => 60, // 1 giờ
            'price_per_hour' => 60000,
        ]);
        TablePricing::create([
            'category_id' => $catStd->id,
            'duration_minutes' => 120, // 2 giờ
            'price_per_hour' => 55000, // Giảm giá
        ]);
    }
}