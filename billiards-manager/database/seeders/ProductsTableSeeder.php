<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        $products = [
            // Đồ uống
            ['name' => 'Coca-Cola', 'category' => 'Drink', 'product_type' => 'Single', 'price' => 20000, 'cost_price' => 12000, 'stock_quantity' => 100, 'min_stock_level' => 20, 'unit' => 'Lon', 'status' => 'Active'],
            ['name' => 'Pepsi', 'category' => 'Drink', 'product_type' => 'Single', 'price' => 20000, 'cost_price' => 12000, 'stock_quantity' => 80, 'min_stock_level' => 20, 'unit' => 'Lon', 'status' => 'Active'],
            ['name' => 'Sting', 'category' => 'Drink', 'product_type' => 'Single', 'price' => 20000, 'cost_price' => 12000, 'stock_quantity' => 60, 'min_stock_level' => 15, 'unit' => 'Lon', 'status' => 'Active'],
            ['name' => 'Trà đào', 'category' => 'Drink', 'product_type' => 'Single', 'price' => 25000, 'cost_price' => 15000, 'stock_quantity' => 50, 'min_stock_level' => 10, 'unit' => 'Ly', 'status' => 'Active'],
            
            // Đồ ăn
            ['name' => 'Bắp rang bơ', 'category' => 'Food', 'product_type' => 'Single', 'price' => 35000, 'cost_price' => 20000, 'stock_quantity' => 30, 'min_stock_level' => 5, 'unit' => 'Phần', 'status' => 'Active'],
            ['name' => 'Khoai tây chiên', 'category' => 'Food', 'product_type' => 'Single', 'price' => 40000, 'cost_price' => 22000, 'stock_quantity' => 25, 'min_stock_level' => 5, 'unit' => 'Phần', 'status' => 'Active'],
            
            // Dịch vụ (Giờ chơi)
            ['name' => 'Giờ chơi bàn Regular', 'category' => 'Service', 'product_type' => 'Single', 'price' => 60000, 'cost_price' => 0, 'stock_quantity' => 9999, 'min_stock_level' => 0, 'unit' => 'Giờ', 'status' => 'Active'],
            ['name' => 'Giờ chơi bàn VIP', 'category' => 'Service', 'product_type' => 'Single', 'price' => 80000, 'cost_price' => 0, 'stock_quantity' => 9999, 'min_stock_level' => 0, 'unit' => 'Giờ', 'status' => 'Active'],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert(array_merge($product, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}