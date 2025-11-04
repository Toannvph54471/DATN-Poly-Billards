<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Promotion;
use App\Models\PromotionApplication;
use Illuminate\Database\Seeder;

class PromotionApplicationSeeder extends Seeder
{
    public function run()
    {
        $bills = Bill::where('final_amount', '>', 0)->get();
        $promotions = Promotion::where('status', 'active')->get();

        if ($promotions->isEmpty()) {
            $this->command->info('No active promotions found!');
            return;
        }

        foreach ($bills as $bill) {
            // Chọn ngẫu nhiên một khuyến mãi
            $promotion = $promotions->random();

            // Giả sử áp dụng giảm giá 10% của hóa đơn, nhưng không vượt quá giá trị khuyến mãi
            $discount = $bill->total_amount * 0.1;
            if ($promotion->discount_type == 'fixed') {
                $discount = min($discount, $promotion->discount_value);
            }

            PromotionApplication::create([
                'bill_id' => $bill->id,
                'promotion_id' => $promotion->id,
                'applied_discount' => $discount
            ]);

            // Cập nhật lại hóa đơn nếu cần (đã có trường discount_amount và final_amount)
            // Trong thực tế, có thể cần tính toán lại, nhưng ở đây chúng ta chỉ seed dữ liệu
        }
    }
}