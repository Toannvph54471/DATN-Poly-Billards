<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->truncate();
        
        // Loại bàn
        Category::create(['name' => 'Bàn Tiêu Chuẩn', 'type' => 'table', 'hourly_rate' => 60000]);
        Category::create(['name' => 'Bàn VIP', 'type' => 'table', 'hourly_rate' => 100000]);
        
        // Loại sản phẩm
        Category::create(['name' => 'Đồ uống', 'type' => 'product']);
        Category::create(attributes: ['name' => 'Đồ ăn vặt', 'type' => 'product']);
        Category::create(['name' => 'Dịch vụ', 'type' => 'product']);
    }
}
