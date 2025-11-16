<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Combo;
use App\Models\BillDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillController extends Controller
{
    /**
     * Show bill detail với payment summary
     */
    public function show($id)
    {
        $bill = Bill::with([
            'table.category',
            'customer',
            'staff',
            'reservation.payments' => function ($query) {
                $query->completed();
            },
            'billDetails.product',
            'billDetails.combo',
            'billTimeUsages',
            'payments' => function ($query) {
                $query->completed();
            }
        ])->findOrFail($id);

        // Calculate payment summary
        $summary = [
            'table_amount' => $bill->calculateTableAmount(),
            'product_amount' => $bill->calculateProductAmount(),
            'subtotal' => $bill->total_amount,
            'discount' => $bill->discount_amount,
            'total' => $bill->final_amount,
            'reservation_paid' => 0,
            'additional_paid' => 0,
            'total_paid' => 0,
            'remaining' => 0,
        ];

        // Get reservation payment
        if ($bill->reservation_id) {
            $summary['reservation_paid'] = $bill->reservation
                ->payments()
                ->completed()
                ->sum('amount');
        }

        // Get additional payments (for products)
        $summary['additional_paid'] = $bill->completedPayments()->sum('amount');
        $summary['total_paid'] = $summary['reservation_paid'] + $summary['additional_paid'];
        $summary['remaining'] = max(0, $summary['total'] - $summary['total_paid']);

        return view('admin.bills.show', compact('bill', 'summary'));
    }

    /**
     * Thêm sản phẩm vào bill
     */
    public function addProduct(Request $request, $billId)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $bill = Bill::findOrFail($billId);

        if (!$bill->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Bill đã đóng, không thể thêm sản phẩm'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $detail = $bill->addProduct(
                $request->product_id,
                $request->quantity
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm',
                'detail' => $detail->load('product'),
                'bill_summary' => $bill->fresh()->payment_summary
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add product error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thêm combo vào bill
     */
    public function addCombo(Request $request, $billId)
    {
        $request->validate([
            'combo_id' => 'required|exists:combos,id',
        ]);

        $bill = Bill::findOrFail($billId);

        if (!$bill->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Bill đã đóng, không thể thêm combo'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $detail = $bill->addCombo($request->combo_id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm combo',
                'detail' => $detail->load('combo'),
                'bill_summary' => $bill->fresh()->payment_summary
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add combo error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật bill detail
     */
    public function updateBillDetail(Request $request, $billId, $detailId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $bill = Bill::findOrFail($billId);
        $detail = $bill->billDetails()->findOrFail($detailId);

        if (!$bill->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Bill đã đóng'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $detail->update([
                'quantity' => $request->quantity,
                'total_price' => $detail->unit_price * $request->quantity
            ]);

            $bill->updateAmounts();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật',
                'detail' => $detail->fresh(),
                'bill_summary' => $bill->fresh()->payment_summary
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa bill detail
     */
    public function deleteBillDetail($billId, $detailId)
    {
        $bill = Bill::findOrFail($billId);

        if (!$bill->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Bill đã đóng'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $bill->removeProduct($detailId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm',
                'bill_summary' => $bill->fresh()->payment_summary
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Đóng bill (before payment)
     */
    public function closeTable(Request $request, $billId)
    {
        $bill = Bill::findOrFail($billId);

        if (!$bill->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Bill không thể đóng'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $bill->close();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã đóng bill. Vui lòng thanh toán.',
                'bill_summary' => $bill->fresh()->payment_summary,
                'redirect' => route('bills.payment', $billId)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Close bill error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show payment form
     */
    public function showPayment($billId)
    {
        $bill = Bill::with([
            'reservation.payments',
            'billDetails.product',
            'billTimeUsages',
            'payments'
        ])->findOrFail($billId);

        if (!$bill->canBePaid()) {
            return redirect()
                ->route('admin.tables.detail', $bill->table_id)
                ->with('error', 'Bill không thể thanh toán');
        }

        $summary = $bill->payment_summary;

        return view('admin.bills.payment', compact('bill', 'summary'));
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request, $billId)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,vnpay,momo,zalopay',
            'amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        $bill = Bill::findOrFail($billId);

        if (!$bill->canBePaid()) {
            return response()->json([
                'success' => false,
                'message' => 'Bill không thể thanh toán'
            ], 400);
        }

        $paymentMethod = $request->payment_method;
        $amount = $request->amount;
        $discount = $request->discount_amount ?? 0;

        // Validate amount
        if ($amount > $bill->remaining_amount + $discount) {
            return response()->json([
                'success' => false,
                'message' => 'Số tiền thanh toán không hợp lệ'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $payment = $bill->pay($paymentMethod, $amount, $discount);

            DB::commit();

            // If cash/card, return success
            if (in_array($paymentMethod, ['cash', 'card'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thanh toán thành công!',
                    'payment' => $payment,
                    'bill_summary' => $bill->fresh()->payment_summary,
                    'is_fully_paid' => $bill->fresh()->isFullyPaid(),
                ]);
            }

            // If online payment, return payment URL
            $paymentUrl = $this->createOnlinePaymentUrl($payment, $paymentMethod);

            return response()->json([
                'success' => true,
                'require_redirect' => true,
                'payment_url' => $paymentUrl
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Process payment error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạm dừng bàn
     */
    public function pauseTable($billId)
    {
        $bill = Bill::findOrFail($billId);

        if ($bill->status !== Bill::STATUS_OPEN) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể tạm dừng bill đang mở'
            ], 400);
        }

        $bill->update([
            'status' => Bill::STATUS_PAUSED,
            'paused_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã tạm dừng'
        ]);
    }

    /**
     * Tiếp tục bàn
     */
    public function resumeTable($billId)
    {
        $bill = Bill::findOrFail($billId);

        if ($bill->status !== Bill::STATUS_PAUSED) {
            return response()->json([
                'success' => false,
                'message' => 'Bill không ở trạng thái tạm dừng'
            ], 400);
        }

        // Calculate pause duration
        if ($bill->paused_at) {
            $pausedMinutes = $bill->paused_at->diffInMinutes(now());
            $bill->increment('paused_duration', $pausedMinutes);
        }

        $bill->update([
            'status' => Bill::STATUS_OPEN,
            'paused_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã tiếp tục'
        ]);
    }

    /**
     * Mở bàn mới (không từ reservation)
     */
    public function openTable(Request $request, $tableId)
    {
        $request->validate([
            'customer_name' => 'nullable|string',
            'customer_phone' => 'nullable|string',
        ]);

        $table = \App\Models\Table::findOrFail($tableId);

        if (!$table->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'Bàn không khả dụng'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create bill
            $bill = Bill::create([
                'bill_number' => 'BILL-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),
                'table_id' => $tableId,
                'customer_id' => null,
                'reservation_id' => null,
                'staff_id' => auth()->id() ?? 1,
                'start_time' => now(),
                'status' => Bill::STATUS_OPEN,
                'total_amount' => 0,
                'final_amount' => 0,
            ]);

            // Create time usage
            $bill->billTimeUsages()->create([
                'start_time' => now(),
                'hourly_rate' => $table->getHourlyRate(),
            ]);

            // Update table status
            $table->markAsOccupied();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã mở bàn',
                'bill' => $bill->load('billTimeUsages')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Open table error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== HELPER METHODS ====================
}
