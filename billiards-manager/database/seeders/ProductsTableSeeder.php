<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->truncate();
        $catDrink = Category::where('name', 'Đồ uống')->first();
        $catFood = Category::where('name', 'Đồ ăn vặt')->first();

        Product::create(['product_code' => 'DR001', 'name' => 'Coca-Cola', 'category_id' => $catDrink->id, 'product_type' => 'Consumption', 'price' => 20000, 'stock_quantity' => 100, 'unit' => 'Lon']);
        Product::create(['product_code' => 'DR002', 'name' => 'Nước suối', 'category_id' => $catDrink->id, 'product_type' => 'Consumption', 'price' => 15000, 'stock_quantity' => 100, 'unit' => 'Chai']);
        Product::create(['product_code' => 'FD001', 'name' => 'Khoai tây chiên', 'category_id' => $catFood->id, 'product_type' => 'Consumption', 'price' => 35000, 'stock_quantity' => 50, 'unit' => 'Phần']);
    }
}
