<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Customer;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run()
    {
        $customers = Customer::limit(10)->get();
        $tables = Table::where('status', 'available')->get();
        $user = User::first();

        if ($tables->isEmpty()) {
            $this->command->info('No available tables found!');
            return;
        }

        foreach ($customers as $customer) {
            $table = $tables->random();
            $reservationTime = now()->addDays(rand(1, 30))->setTime(rand(10, 20), 0, 0);

            Reservation::create([
                'reservation_code' => 'RSV' . date('YmdHis') . rand(100, 999),
                'customer_id' => $customer->id,
                'table_id' => $table->id,
                'reservation_time' => $reservationTime,
                'end_time' => $reservationTime->copy()->addHours(2),
                'duration' => 120,
                'guest_count' => rand(1, $table->max_guests ?? 4),
                'note' => 'Seeder reservation',
                'status' => Reservation::STATUS_PENDING,
                'created_by' => $user->id,
            ]);
        }
    }
}