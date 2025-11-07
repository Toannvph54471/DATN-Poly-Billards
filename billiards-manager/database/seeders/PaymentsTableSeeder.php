<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bill;
use Illuminate\Support\Facades\DB;

class PaymentsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('payments')->truncate();
        
        // Chúng ta sẽ seed một thanh toán cho hóa đơn đã hoàn thành (nếu có)
        // $closedBill = Bill::where('status', 'Closed')->first();
        // if ($closedBill) {
        //     DB::table('payments')->insert([
        //         'bill_id' => $closedBill->id,
        //         'amount' => $closedBill->final_amount,
        //         'payment_method' => 'Cash',
        //         'status' => 'Success',
        //         'paid_at' => $closedBill->end_time,
        //     ]);
        // }
    }
}