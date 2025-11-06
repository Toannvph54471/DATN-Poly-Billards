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
            // Đồ uống
            [
                'product_code' => 'DRK001',
                'name' => 'Coca Cola',
                'category_name' => 'Nước ngọt',
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
                'category_name' => 'Nước ngọt',
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
                'category_name' => 'Snack',
                'product_type' => 'Consumption',
                'price' => 15000,
                'cost_price' => 8000,
                'stock_quantity' => 50,
                'min_stock_level' => 5,
                'unit' => 'gói',
                'status' => 'Active'
            ]
        ];

        foreach ($products as $p) {
            $category = Category::where('name', $p['category_name'])->first();

            if (!$category) {
                continue; // Bỏ qua nếu category không tồn tại
            }

            Product::firstOrCreate(
                ['product_code' => $p['product_code']], // Kiểm tra theo mã duy nhất
                [
                    'name' => $p['name'],
                    'category_id' => $category->id,
                    'product_type' => $p['product_type'],
                    'price' => $p['price'],
                    'cost_price' => $p['cost_price'],
                    'stock_quantity' => $p['stock_quantity'],
                    'min_stock_level' => $p['min_stock_level'],
                    'unit' => $p['unit'],
                    'status' => $p['status'],
                ]
            );
        }
    }
}
