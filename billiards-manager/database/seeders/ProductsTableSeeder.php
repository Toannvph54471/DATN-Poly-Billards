<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Dịch vụ
            [
                'product_code' => 'SERVICE001',
                'name' => 'Giờ chơi bàn Standard',
                'category_id' => Category::where('name', 'Dịch vụ')->first()->id,
                'product_type' => 'Service',
                'price' => 50000,
                'cost_price' => 0,
                'stock_quantity' => 9999,
                'min_stock_level' => 0,
                'unit' => 'giờ',
                'status' => 'Active',
            ],
            [
                'product_code' => 'SERVICE002',
                'name' => 'Giờ chơi bàn VIP',
                'category_id' => Category::where('name', 'Dịch vụ')->first()->id,
                'product_type' => 'Service',
                'price' => 80000,
                'cost_price' => 0,
                'stock_quantity' => 9999,
                'min_stock_level' => 0,
                'unit' => 'giờ',
                'status' => 'Active',
            ],
            [
                'product_code' => 'SERVICE003',
                'name' => 'Giờ chơi bàn Competition',
                'category_id' => Category::where('name', 'Dịch vụ')->first()->id,
                'product_type' => 'Service',
                'price' => 100000,
                'cost_price' => 0,
                'stock_quantity' => 9999,
                'min_stock_level' => 0,
                'unit' => 'giờ',
                'status' => 'Active',
            ],

            // Đồ uống
            [
                'product_code' => 'DRK001',
                'name' => 'Coca Cola',
                'category_id' => Category::where('name', 'Nước ngọt')->first()->id,
                'product_type' => 'Consumption',
                'price' => 20000,
                'cost_price' => 12000,
                'stock_quantity' => 100,
                'min_stock_level' => 10,
                'unit' => 'chai',
                'status' => 'Active'
            ],
            [
                'product_code' => 'DRK002',
                'name' => 'Pepsi',
                'category_id' => Category::where('name', 'Nước ngọt')->first()->id,
                'product_type' => 'Consumption',
                'price' => 20000,
                'cost_price' => 12000,
                'stock_quantity' => 80,
                'min_stock_level' => 10,
                'unit' => 'chai',
                'status' => 'Active'
            ],

            // Đồ ăn
            [
                'product_code' => 'FOD001',
                'name' => 'Bim bim',
                'category_id' => Category::where('name', 'Snack')->first()->id,
                'product_type' => 'Consumption',
                'price' => 15000,
                'cost_price' => 8000,
                'stock_quantity' => 50,
                'min_stock_level' => 5,
                'unit' => 'gói',
                'status' => 'Active'
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
