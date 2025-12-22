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
        // Xóa dữ liệu cũ
        DB::table('tables')->truncate();

        // Lấy bảng giá theo tên đã seed
        $kkKing     = TableRate::where('name', 'KKKing')->first();
        $mrSung     = TableRate::where('name', 'MRSung')->first();
        $chinese    = TableRate::where('name', 'Chinse Pool')->first();
        $diamond    = TableRate::where('name', 'Diamond')->first();
        $snookers   = TableRate::where('name', 'Snookers')->first();
        $vip        = TableRate::where('name', 'Bàn Vip')->first();

        // ===== Khu A =====
        Table::create([
            'table_number'  => 'A01',
            'table_name'    => 'Bàn A01',
            'table_rate_id' => $kkKing?->id,
            'status'        => 'available',
        ]);

        Table::create([
            'table_number'  => 'A02',
            'table_name'    => 'Bàn A02',
            'table_rate_id' => $mrSung?->id,
            'status'        => 'available',
        ]);

        // ===== Khu B =====
        Table::create([
            'table_number'  => 'B01',
            'table_name'    => 'Bàn Chinese Pool',
            'table_rate_id' => $chinese?->id,
            'status'        => 'available',
        ]);

        Table::create([
            'table_number'  => 'B02',
            'table_name'    => 'Bàn Diamond',
            'table_rate_id' => $diamond?->id,
            'status'        => 'available',
        ]);

        // ===== Khu VIP =====
        Table::create([
            'table_number'  => 'V01',
            'table_name'    => 'Bàn VIP 01',
            'table_rate_id' => $vip?->id,
            'status'        => 'available',
        ]);

        Table::create([
            'table_number'  => 'V02',
            'table_name'    => 'Bàn VIP 02',
            'table_rate_id' => $vip?->id,
            'status'        => 'maintenance',
        ]);

        // ===== Snookers =====
        Table::create([
            'table_number'  => 'S01',
            'table_name'    => 'Bàn Snookers',
            'table_rate_id' => $snookers?->id,
            'status'        => 'available',
        ]);
    }
}