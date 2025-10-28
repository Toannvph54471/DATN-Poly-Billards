<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Promotion;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('promotions')->insert([
            [
                'promotion_code' => 'SALE10',
                'name' => 'Giảm 10% toàn menu',
                'description' => 'Áp dụng cho tất cả sản phẩm và combo',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'start_date' => now(),
                'end_date' => now()->addDays(7),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'promo_code' => 'FREEDRINK',
                'name' => 'Tặng nước miễn phí',
                'description' => 'Áp dụng cho hóa đơn trên 300.000đ',
                'discount_type' => 'fixed',
                'discount_value' => 30_000,
                'start_date' => now(),
                'end_date' => now()->addDays(10),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'promo_code' => 'COMBO20',
                'name' => 'Giảm 20% combo giờ chơi',
                'description' => 'Áp dụng cho tất cả combo trong khung giờ 10h-15h',
                'discount_type' => 'percent',
                'discount_value' => 20,
                'start_date' => now(),
                'end_date' => now()->addDays(5),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
