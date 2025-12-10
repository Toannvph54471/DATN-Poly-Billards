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
        
        // Loại sản phẩm
        Category::create(['name' => 'Đồ uống', 'type' => 'product']);
        Category::create(attributes: ['name' => 'Đồ ăn vặt', 'type' => 'product']);
        Category::create(['name' => 'Dịch vụ', 'type' => 'product']);
    }
}
