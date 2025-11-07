<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bill;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class BillTimeUsageTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bill_time_usage')->truncate();
        
        $bill = Bill::where('bill_number', 'BILL2025001')->first();
        // Giả sử bill này dùng bàn VIP
        $catVip = Category::where('name', 'Bàn VIP')->first();

        if ($bill && $catVip) {
            DB::table('bill_time_usage')->insert([
                'bill_id' => $bill->id,
                'start_time' => $bill->start_time,
                'end_time' => null, // Bàn đang mở
                'duration_minutes' => null,
                'hourly_rate' => $catVip->hourly_rate,
                'total_price' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}