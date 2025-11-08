<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Customer;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservationsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('reservations')->truncate();
        
        $customer = User::where('email', 'nguyenvana@gmail.com')->first();
        $table = Table::where('status', 'available')->first();
        
        if ($customer && $table) {
            Reservation::create([
                'reservation_code' => 'RSV2025001',
                'table_id' => $table->id,
                'customer_id' => $customer->id,
                'reservation_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2),
                'duration' => 120,
                'guest_count' => 2,
                'status' => 'Confirmed',
            ]);
        }
    }
}