<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\BillTimeUsage;
use App\Models\Combo;
use App\Models\ComboTimeUsage;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    // 1. Mở Bàn
    public function openTable($id)
    {
        $table = Table::findOrFail($id);

        if ($table->status === 'in_use') {
            return response()->json(['message' => 'Bàn đang được sử dụng'], 400);
        }

        return DB::transaction(function () use ($table) {
            $staffId = Auth::check() ? Auth::id() : 1;

            $bill = Bill::create([
                'bill_number' => 'BILL-' . strtoupper(uniqid()),
                'table_id' => $table->id,
                'customer_id' => null,
                'staff_id' => $staffId,
                'start_time' => now(),
                'end_time' => null,
                'status' => 'Open',
                'total_amount' => 0,
                'final_amount' => 0,
            ]);

            // Chỉ cần bill_id + start_time
            // hourly_rate sẽ tự lấy default(0) từ DB
            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $table->hourly_rate ?? 0,
                // hourly_rate: để DB tự điền default(0)
                // end_time, duration_minutes, total_price: nullable → để NULL
            ]);

            $table->update(['status' => 'in_use']);

            return response()->json([
                'message' => 'Mở bàn thành công',
                'bill' => $bill->load('staff', 'table')
            ], 201);
        });
    }
    // 2. THÊM SẢN PHẨM VÀO BÀN (Multiple products)
    public function addProduct(Request $request, $billId)
    {
        $bill = Bill::findOrFail($billId);
        if ($bill->status !== 'Open') {
            return redirect()->back()->with('error', 'Bill đã đóng');
        }

        $products = $request->input('products', []);
        $addedProducts = 0;

        DB::transaction(function () use ($products, $bill, &$addedProducts) {
            foreach ($products as $productId => $quantity) {
                $quantity = intval($quantity);

                // CHỈ XỬ LÝ NẾU quantity >= 1
                if ($quantity < 1) continue;

                $product = Product::findOrFail($productId);

                if ($product->stock_quantity < $quantity) {
                    throw new \Exception("Không đủ hàng: {$product->name}");
                }

                $totalPrice = $product->price * $quantity;

                BillDetail::create([
                    'bill_id' => $bill->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'original_price' => $product->price,
                    'total_price' => $totalPrice,
                ]);

                $bill->increment('total_amount', $totalPrice);
                $product->decrement('stock_quantity', $quantity);
                $addedProducts++;
            }
        });

        $message = $addedProducts > 0
            ? "Đã thêm {$addedProducts} sản phẩm vào order"
            : "Không có sản phẩm nào được thêm";

        return redirect()->back()->with('success', $message);
    }


    // 4. DỪNG BÀN
    public function pauseTable($billId)
    {
        try {
            DB::transaction(function () use ($billId) {
                $bill = Bill::findOrFail($billId);
                if ($bill->status !== 'Open') {
                    throw new \Exception('Chỉ có thể tạm dừng khi đang mở');
                }

                $bill->update([
                    'status' => 'Paused',
                    'paused_at' => now(),
                ]);
            });

            return response()->json(['success' => true, 'message' => 'Đã tạm dừng bàn']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function resumeTable($billId)
    {
        try {
            DB::transaction(function () use ($billId) {
                $bill = Bill::findOrFail($billId);

                if ($bill->status !== 'Paused') {
                    throw new \Exception('Chỉ có thể tiếp tục khi đang tạm dừng.');
                }

                if ($bill->paused_at) {
                    $pausedMinutes = now()->diffInMinutes($bill->paused_at);
                    $bill->paused_duration += $pausedMinutes;
                }

                $bill->update([
                    'status' => 'Open',
                    'paused_at' => null,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Đã tiếp tục bàn.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function closeTable($billId)
    {
        try {
            DB::transaction(function () use ($billId) {
                $bill = Bill::findOrFail($billId);

                if (!in_array($bill->status, ['Open', 'Paused'])) {
                    throw new \Exception('Bill đã đóng hoặc không hợp lệ.');
                }

                // === 1. Nếu đang tạm dừng thì cộng thêm thời gian dừng ===
                if ($bill->status === 'Paused' && $bill->paused_at) {
                    $pausedMinutes = now()->diffInMinutes($bill->paused_at);
                    $bill->paused_duration += $pausedMinutes;
                }

                // === 2. Lấy record BillTimeUsage hiện tại ===
                $usage = BillTimeUsage::where('bill_id', $bill->id)
                    ->whereNull('end_time')
                    ->firstOrFail();

                // === 3. Cập nhật thời gian kết thúc ===
                $usage->update([
                    'end_time' => now(),
                ]);

                // === 4. Dùng accessor để lấy tổng phút thực tế ===
                $totalMinutes = max(0, $usage->duration_minutes - $bill->paused_duration);

                // === 5. Tính tiền bàn (giá/giờ) ===
                $hourlyRate = $usage->hourly_rate ?? 0;
                $tablePrice = round(($totalMinutes / 60) * $hourlyRate);

                // === 6. Cập nhật tiền bàn và thời lượng vào DB ===
                $usage->update([
                    'duration_minutes' => $totalMinutes,
                    'total_price' => $tablePrice,
                ]);

                // === 7. Cộng tiền bàn vào tổng hóa đơn ===
                $bill->increment('total_amount', $tablePrice);

                // === 8. Cập nhật trạng thái bill và bàn ===
                $bill->update([
                    'status' => 'Closed',
                    'end_time' => now(),
                    'paused_at' => null,
                    'final_amount' => $bill->total_amount,
                ]);

                $bill->table?->update(['status' => 'available']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Thanh toán thành công! Đã tính tiền bàn.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
