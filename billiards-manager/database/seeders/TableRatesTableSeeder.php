<?php

namespace Database\Seeders;

use App\Models\TableRate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableRatesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa dữ liệu cũ
        DB::table('table_rates')->truncate();

        // Tạo bảng giá Regular
        TableRate::create([
            'code' => 'REGULAR_WEEKDAY',
            'name' => 'Giờ ngày thường (Regular)',
            'hourly_rate' => 50000,
        ]);

        TableRate::create([
            'code' => 'REGULAR_WEEKEND',
            'name' => 'Giờ cuối tuần (Regular)',
            'hourly_rate' => 60000,
        ]);

        // Tạo bảng giá VIP
        TableRate::create([
            'code' => 'VIP_WEEKDAY',
            'name' => 'Giờ ngày thường (VIP)',
            'hourly_rate' => 100000,
        ]);

        TableRate::create([
            'code' => 'VIP_WEEKEND',
            'name' => 'Giờ cuối tuần (VIP)',
            'hourly_rate' => 120000,
        ]);

        // Nếu muốn thêm các loại bảng giá khác, ví dụ Competition
        TableRate::create([
            'code' => 'COMPETITION_WEEKDAY',
            'name' => 'Giờ ngày thường (Competition)',
            'hourly_rate' => 80000,
        ]);

        TableRate::create([
            'code' => 'COMPETITION_WEEKEND',
            'name' => 'Giờ cuối tuần (Competition)',
            'hourly_rate' => 90000,
        ]);
    }
}
