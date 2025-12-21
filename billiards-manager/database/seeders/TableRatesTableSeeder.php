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
            'code' => '001',
            'name' => 'KKKing',
            'hourly_rate' => 50000,
        ]);

        TableRate::create([
            'code' => '002',
            'name' => 'MRSung',
            'hourly_rate' => 60000,
        ]);

        // Tạo bảng giá VIP
        TableRate::create([
            'code' => '003',
            'name' => 'Chinse Pool',
            'hourly_rate' => 100000,
        ]);

        TableRate::create([
            'code' => '004',
            'name' => 'Diamond',
            'hourly_rate' => 120000,
        ]);

        // Nếu muốn thêm các loại bảng giá khác, ví dụ Competition
        TableRate::create([
            'code' => '005',
            'name' => 'Snookers',
            'hourly_rate' => 80000,
        ]);

        TableRate::create([
            'code' => '006',
            'name' => 'Bàn Vip',
            'hourly_rate' => 90000,
        ]);
    }
}
