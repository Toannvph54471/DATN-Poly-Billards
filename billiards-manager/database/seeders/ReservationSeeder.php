<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $tables = Table::inRandomOrder()->take(3)->get();
        if ($tables->count() < 3) {
            $this->call(TablesTableSeeder::class);
            $tables = Table::inRandomOrder()->take(3)->get();
        }

        $now = Carbon::now();
        $baseDate = $now->format('Ymd');

        $reservations = [
            [
                'table_id' => $tables[0]->id,
                'customer_name' => 'Nguyễn Văn A',
                'customer_phone' => '0901234567',
                'reservation_time' => $now->copy()->addHours(2),
                'end_time' => $now->copy()->addHours(4),
                'duration' => 120,
                'guest_count' => 4,
                'total_amount' => 160000,
                'deposit_amount' => 160000,
                'payment_type' => 'deposit',
                'status' => 'pending',
                'created_by' => 1,
            ],
            [
                'table_id' => $tables[1]->id,
                'customer_name' => 'Trần Thị B',
                'customer_phone' => '0912345678',
                'reservation_time' => $now->copy()->addHours(3),
                'end_time' => $now->copy()->addHours(6),
                'duration' => 180,
                'guest_count' => 6,
                'total_amount' => 360000,
                'deposit_amount' => 360000,
                'payment_type' => 'full',
                'status' => 'confirmed',
                'created_by' => 1,
            ],
            [
                'table_id' => $tables[2]->id,
                'customer_name' => 'Lê Văn C',
                'customer_phone' => '0923456789',
                'reservation_time' => $now->copy()->addHours(1),
                'end_time' => $now->copy()->addHours(3),
                'duration' => 120,
                'guest_count' => 3,
                'total_amount' => 240000,
                'deposit_amount' => 240000,
                'payment_type' => 'deposit',
                'status' => 'pending',
                'created_by' => 1,
            ],
        ];

        foreach ($reservations as $index => $r) {
            // Tạo mã duy nhất: RSV + ngày + số thứ tự (3 chữ số)
            $code = 'RSV' . $baseDate . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);

            // Nếu đã tồn tại → thêm random
            while (Reservation::where('reservation_code', $code)->exists()) {
                $code = 'RSV' . $baseDate . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT) . mt_rand(10, 99);
            }

            $r['reservation_code'] = $code;
            Reservation::create($r);
        }
    }
}
