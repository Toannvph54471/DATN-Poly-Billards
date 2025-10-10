<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('shifts')->insert([
            [
                'name' => 'Ca sáng',
                'start_time' => '07:00:00',
                'end_time' => '12:00:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Ca chiều',
                'start_time' => '12:00:00',
                'end_time' => '17:00:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Ca tối',
                'start_time' => '17:00:00',
                'end_time' => '22:00:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}