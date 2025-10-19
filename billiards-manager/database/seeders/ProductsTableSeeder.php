<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        $products = [
            // Dịch vụ thuê bàn theo giờ
            [
                'product_code' => 'SERVICE001',
                'name' => 'Giờ chơi bàn Standard',
                'product_type' => Product::TYPE_SERVICE, // ← TYPE MỚI
                'category' => 'Dịch vụ',
                'price' => 50000,
                'cost_price' => 0, // Không có chi phí
                'stock_quantity' => 9999, // Không giới hạn
                'min_stock_level' => 0,
                'unit' => 'giờ',
                'status' => 'Active',
            ],
            [
                'product_code' => 'SERVICE002',
                'name' => 'Giờ chơi bàn VIP',
                'product_type' => Product::TYPE_SERVICE,
                'category' => 'Dịch vụ',
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
                'product_type' => Product::TYPE_SERVICE,
                'category' => 'Dịch vụ',
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
                'product_type' => Product::TYPE_DRINK,
                'category' => 'Nước ngọt',
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
                'product_type' => Product::TYPE_DRINK,
                'category' => 'Nước ngọt',
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
                'product_type' => Product::TYPE_FOOD,
                'category' => 'Snack',
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
