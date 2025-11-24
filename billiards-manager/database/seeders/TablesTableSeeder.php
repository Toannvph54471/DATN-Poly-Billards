<?php

namespace Database\Seeders;

use App\Models\Table;
use App\Models\TableRate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TablesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa tất cả dữ liệu cũ
        DB::table('tables')->truncate();

        // Lấy các bảng giá hiện có
        $regularRate = TableRate::where('name', 'Regular')->first();
        $vipRate = TableRate::where('name', 'VIP')->first();

        // Tạo bàn mới với table_rate_id
        Table::create([
            'table_number' => 'A01',
            'table_name' => 'Bàn A01',
            'table_rate_id' => $regularRate->id ?? null,
            'status' => 'available'
        ]);

        Table::create([
            'table_number' => 'A02',
            'table_name' => 'Bàn A02',
            'table_rate_id' => $regularRate->id ?? null,
            'status' => 'available'
        ]);
        Table::create([
            'table_number' => 'V01',
            'table_name' => 'Bàn VIP 01',
            'table_rate_id' => $vipRate->id ?? null,
            'status' => 'available'
        ]);

        Table::create([
            'table_number' => 'V02',
            'table_name' => 'Bàn VIP 02',
            'table_rate_id' => $vipRate->id ?? null,
            'status' => 'maintenance'
        ]);
    }
}
