<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TablesTableSeeder extends Seeder
{
    public function run()
    {
        // Bàn Regular (60k/giờ)
        for ($i = 1; $i <= 10; $i++) {
            DB::table('tables')->insert([
                'table_number' => 'B' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'type' => 'Regular',
                'status' => 'Available',
                'hourly_rate' => 60000,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Bàn VIP (80k/giờ)
        for ($i = 1; $i <= 4; $i++) {
            DB::table('tables')->insert([
                'table_number' => 'V' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'type' => 'VIP',
                'status' => 'Available',
                'hourly_rate' => 80000,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}