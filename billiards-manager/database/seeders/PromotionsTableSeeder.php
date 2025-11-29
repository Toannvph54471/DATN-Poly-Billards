<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PromotionsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại tạm thời
        Schema::disableForeignKeyConstraints();
        DB::table('promotions')->delete();
        Schema::enableForeignKeyConstraints();

        // Reset ID auto-increment
        DB::statement('ALTER TABLE promotions AUTO_INCREMENT = 1');

        $promotions = [
            [
                'promotion_code' => 'WELCOME10',
                'name' => 'Giảm 10% cho khách hàng mới',
                'description' => 'Giảm 10% cho hóa đơn đầu tiên của khách hàng mới',
                'discount_type' => 'percent',
                'discount_value' => '10.00',
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'applies_to_combo' => 1,
                'applies_to_time_combo' => 1,
                'min_play_minutes' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'promotion_code' => 'HAPPY_HOUR',
                'name' => 'Giờ vàng 15%',
                'description' => 'Giảm 15% vào khung giờ 14:00 - 17:00 các ngày trong tuần',
                'discount_type' => 'percent',
                'discount_value' => '15.00',
                'start_date' => now(),
                'end_date' => now()->addMonths(6),
                'applies_to_combo' => 0,
                'applies_to_time_combo' => 1,
                'min_play_minutes' => 60,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'promotion_code' => 'STUDENT20',
                'name' => 'Giảm 20% cho sinh viên',
                'description' => 'Giảm 20% cho sinh viên có thẻ sinh viên hợp lệ',
                'discount_type' => 'percent',
                'discount_value' => '20.00',
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'applies_to_combo' => 1,
                'applies_to_time_combo' => 1,
                'min_play_minutes' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'promotion_code' => 'VIP_MEMBER',
                'name' => 'Ưu đãi thành viên VIP',
                'description' => 'Giảm 25.000 VND cho thành viên VIP',
                'discount_type' => 'fixed',
                'discount_value' => '25000.00',
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'applies_to_combo' => 1,
                'applies_to_time_combo' => 0,
                'min_play_minutes' => 120,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'promotion_code' => 'WEEKEND_SPECIAL',
                'name' => 'Ưu đãi cuối tuần',
                'description' => 'Giảm 10% cho tất cả hóa đơn vào cuối tuần',
                'discount_type' => 'percent',
                'discount_value' => '10.00',
                'start_date' => now()->startOfWeek(),
                'end_date' => now()->addMonths(3),
                'applies_to_combo' => 1,
                'applies_to_time_combo' => 1,
                'min_play_minutes' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('promotions')->insert($promotions);
    }
}