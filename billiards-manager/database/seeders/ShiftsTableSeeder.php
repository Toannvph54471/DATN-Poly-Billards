<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;

class ShiftsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('shifts')->truncate();
        Shift::create(['name' => 'Ca Sáng', 'start_time' => '08:00:00', 'end_time' => '16:00:00']);
        Shift::create(['name' => 'Ca Tối', 'start_time' => '16:00:00', 'end_time' => '00:00:00']);
        Shift::create(['name' => 'Ca Gãy', 'start_time' => '12:00:00', 'end_time' => '20:00:00']);
    }
}