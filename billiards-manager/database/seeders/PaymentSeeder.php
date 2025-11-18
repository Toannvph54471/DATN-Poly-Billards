<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ĐẢM BẢO ROLE
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'description' => 'Quản trị viên cao nhất']
        );

        $employeeRole = Role::firstOrCreate(
            ['slug' => 'employee'],
            ['name' => 'Employee', 'description' => 'Nhân viên']
        );

        // 2. TẠO USER
        $admin = User::firstOrCreate(
            ['email' => 'admin@bia.com'],
            [
                'name' => 'Admin Bida',
                'password' => bcrypt('password'),
                'role_id' => $adminRole->id,
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@bia.com'],
            [
                'name' => 'Nhân viên quầy',
                'password' => bcrypt('password'),
                'role_id' => $employeeRole->id,
            ]
        );

        // 3. ĐẢM BẢO CÓ RESERVATION
        if (Reservation::count() === 0) {
            $this->call(ReservationSeeder::class);
        }

        $reservations = Reservation::inRandomOrder()->take(3)->get();

        // 4. TẠO THANH TOÁN
        $payments = [
            [
                'reservation_id'   => $reservations[0]->id,
                'amount'           => 160000.00,
                'currency'         => 'VND',
                'payment_method'   => 'vnpay',
                'payment_type'     => 'deposit',
                'status'           => 'completed',
                'transaction_id'   => 'VNP' . strtoupper(substr(uniqid(), -8)),
                'payment_url'      => 'https://sandbox.vnpayment.vn/...',
                'payment_data'     => json_encode(['vnp_TxnRef' => '123456']),
                'paid_at'          => now()->subHours(3),
                'completed_at'     => now()->subHours(3),
                'processed_by'     => $admin->id,
            ],
            [
                'reservation_id'   => $reservations[1]->id,
                'amount'           => 360000.00,
                'currency'         => 'VND',
                'payment_method'   => 'momo',
                'payment_type'     => 'full',
                'status'           => 'completed',
                'transaction_id'   => 'MM' . rand(100000, 999999),
                'paid_at'          => now()->subDays(1),
                'completed_at'     => now()->subDays(1),
                'processed_by'     => $staff->id,
            ],
            [
                'reservation_id'   => $reservations[2]->id,
                'amount'           => 240000.00,
                'currency'         => 'VND',
                'payment_method'   => 'vnpay',
                'payment_type'     => 'deposit',
                'status'           => 'pending',
                'transaction_id'   => 'VNP' . strtoupper(substr(uniqid(), -8)),
                'payment_url'      => 'https://sandbox.vnpayment.vn/...',
            ],
        ];

        foreach ($payments as $p) {
            Payment::create($p);
        }

        // 5. CẬP NHẬT RESERVATION (AN TOÀN)
        $completed = Payment::with('reservation')
            ->where('status', 'completed')
            ->where('payment_type', 'deposit')
            ->first();

        if ($completed && $completed->reservation) {
            $completed->reservation->update([
                'payment_status'       => 'paid',
                'payment_gateway'      => $completed->payment_method,
                'transaction_id'       => $completed->transaction_id,
                'payment_completed_at' => $completed->completed_at,
                'status'               => 'confirmed',
            ]);
        }
    }
}
