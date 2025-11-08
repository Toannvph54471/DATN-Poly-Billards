<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bill;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class BillDetailsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bill_details')->truncate();

        $bill = Bill::where('bill_number', 'BILL2025001')->first();
        $coca = Product::where('product_code', 'DR001')->first();
        $fries = Product::where('product_code', 'FD001')->first();

        if ($bill && $coca && $fries) {
            DB::table('bill_details')->insert([
                [
                    'bill_id' => $bill->id,
                    'product_id' => $coca->id,
                    'quantity' => 2,
                    'unit_price' => $coca->price,
                    'original_price' => $coca->price,
                    'total_price' => $coca->price * 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'bill_id' => $bill->id,
                    'product_id' => $fries->id,
                    'quantity' => 1,
                    'unit_price' => $fries->price,
                    'original_price' => $fries->price,
                    'total_price' => $fries->price * 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }
}