<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
// use App\Models\Bill;
// use App\Models\Combo;
// use App\Models\Table;

class ComboTimeUsagesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('combo_time_usages')->truncate();
        
        // $bill = Bill::first();
        // $combo = Combo::where('is_time_combo', true)->first();
        // $table = Table::first();

        // if ($bill && $combo && $table) {
        //     DB::table('combo_time_usages')->insert([
        //         'combo_id' => $combo->id,
        //         'bill_id' => $bill->id,
        //         'table_id' => $table->id,
        //         'start_time' => $bill->start_time,
        //         'total_minutes' => $combo->play_duration_minutes,
        //         'remaining_minutes' => $combo->play_duration_minutes,
        //     ]);
        // }
    }
}