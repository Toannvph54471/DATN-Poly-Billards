<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
// use App\Models\Bill;
// use App\Models\Promotion;

class PromotionApplicationsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('promotion_applications')->truncate();
        
        // $bill = Bill::first();
        // $promo = Promotion::first();
        // if ($bill && $promo) {
        //     DB::table('promotion_applications')->insert([
        //         'bill_id' => $bill->id,
        //         'promotion_id' => $promo->id,
        //         'applied_discount' => 15000, // (10% của 150.000)
        //     ]);
        // }
    }
}