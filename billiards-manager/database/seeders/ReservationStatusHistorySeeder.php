<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\ReservationStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservationStatusHistorySeeder extends Seeder
{
    public function run()
    {
        $reservations = Reservation::all();
        $user = User::first();

        foreach ($reservations as $reservation) {
            ReservationStatusHistory::create([
                'reservation_id' => $reservation->id,
                'old_status' => null,
                'new_status' => $reservation->status,
                'changed_by' => $user->id,
                'note' => 'Initial status'
            ]);
        }
    }
}