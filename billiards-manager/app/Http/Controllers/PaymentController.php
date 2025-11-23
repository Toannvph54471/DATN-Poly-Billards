<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\BillTimeUsage;
use App\Models\ComboTimeUsage;
use App\Models\Payment;
use App\Models\DailyReport;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Hiển thị trang thanh toán
     */
    public function showPayment($id)
    {
        $bill = Bill::with([
            'table',
            'user',
            'billDetails.product',
            'billDetails.combo'
        ])->findOrFail($id);

        // Tính toán chi phí giờ chơi
        $timeCost = $this->calculateTimeCharge($bill);

        // Tính tổng tiền sản phẩm và combo
        $productTotal = BillDetail::where('bill_id', $bill->id)
            ->where('is_combo_component', false)
            ->sum('total_price');

        return view('admin.payments.payment', compact('bill', 'timeCost', 'productTotal'));
    }

    /**
     * Xử lý thanh toán
     */
    public function processPayment(Request $request, $billId)
    {
        $request->validate([
            'payment_method' => 'required|string|in:cash,bank_transfer,card,vnpay,momo',
            'discount_amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:500',
            'auto_print' => 'nullable|boolean' // Thêm field auto_print
        ]);

        try {
            return DB::transaction(function () use ($request, $billId) {
                $paymentMethod = $request->payment_method;
                $discountAmount = $request->discount_amount ?? 0;
                $note = $request->note;
                $autoPrint = $request->boolean('auto_print', true); // Mặc định là true

                // 1. Lấy bill - cho phép cả Open và quick status
                $bill = Bill::with(['table.tableRate', 'billDetails.product', 'billDetails.combo'])
                    ->whereIn('status', ['Open', 'quick'])
                    ->where('payment_status', 'Pending')
                    ->findOrFail($billId);

                $isQuickBill = $bill->status === 'quick';

                // 2. Xử lý thời gian chơi (chỉ cho bill Open, không xử lý cho quick)
                $timePrice = 0;
                $totalMinutesPlayed = 0;
                $endTime = now();

                if (!$isQuickBill) {
                    // TÌM BillTimeUsage
                    $timeUsage = BillTimeUsage::where('bill_id', $bill->id)
                        ->whereNull('end_time')
                        ->first();

                    if ($timeUsage) {
                        // Có BillTimeUsage đang chạy -> tính tiền giờ
                        $startTime = Carbon::parse($timeUsage->start_time);
                        $totalMinutesPlayed = $startTime->diffInMinutes($endTime);

                        // Trừ thời gian pause nếu có
                        if ($timeUsage->paused_duration) {
                            $totalMinutesPlayed -= $timeUsage->paused_duration;
                        }

                        $hourlyRate = $timeUsage->hourly_rate ?? ($bill->table->tableRate->hourly_rate ?? 0);
                        $timePrice = round(($totalMinutesPlayed / 60) * $hourlyRate, 2);

                        // Cập nhật BillTimeUsage
                        $timeUsage->update([
                            'end_time' => $endTime,
                            'duration_minutes' => $totalMinutesPlayed,
                            'total_price' => $timePrice,
                        ]);
                    } else {
                        // Không có BillTimeUsage đang chạy, kiểm tra xem có BillTimeUsage đã kết thúc không
                        $endedTimeUsage = BillTimeUsage::where('bill_id', $bill->id)
                            ->whereNotNull('end_time')
                            ->first();

                        if ($endedTimeUsage) {
                            // Sử dụng giá trị từ BillTimeUsage đã kết thúc
                            $timePrice = $endedTimeUsage->total_price ?? 0;
                            $totalMinutesPlayed = $endedTimeUsage->duration_minutes ?? 0;
                        }
                        // Nếu không có cả hai, timePrice vẫn = 0 (trường hợp bill chưa tính giờ)
                    }
                }

                // 3. Tính tiền sản phẩm (không tính thành phần combo)
                $productTotal = $bill->billDetails()
                    ->where('is_combo_component', 0)
                    ->sum('total_price');

                // 4. Tổng tiền trước giảm giá
                $totalAmount = $isQuickBill ? $productTotal : ($timePrice + $productTotal);

                // 5. Áp dụng giảm giá
                $discountAmount = max(0, (float) $discountAmount);
                $finalAmount = max(0, $totalAmount - $discountAmount);

                // 6. Cập nhật bill
                $bill->update([
                    'end_time' => $isQuickBill ? $bill->end_time : $endTime,
                    'total_amount' => $totalAmount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $finalAmount,
                    'payment_method' => $paymentMethod,
                    'payment_status' => 'Paid',
                    'status' => 'Closed',
                    'note' => $note ?? $bill->note,
                ]);

                // 7. Lưu thanh toán vào bảng payments
                Payment::create([
                    'bill_id' => $bill->id,
                    'amount' => $finalAmount,
                    'currency' => 'VND',
                    'payment_method' => $paymentMethod,
                    'payment_type' => 'full',
                    'status' => 'completed',
                    'transaction_id' => 'BILL_' . $bill->bill_number . '_' . now()->format('YmdHis'),
                    'paid_at' => now(),
                    'completed_at' => now(),
                    'processed_by' => Auth::id(),
                    'note' => $note,
                    'payment_data' => json_encode([
                        'bill_number' => $bill->bill_number,
                        'table' => $bill->table->table_number,
                        'bill_type' => $isQuickBill ? 'quick' : 'regular',
                        'play_minutes' => $isQuickBill ? 0 : $totalMinutesPlayed,
                        'time_price' => $timePrice,
                        'product_total' => $productTotal,
                        'discount' => $discountAmount,
                    ]),
                ]);

                // 8. Giải phóng bàn
                $bill->table->update(['status' => 'available']);

                // 9. Cập nhật báo cáo hàng ngày
                $this->updateDailyReport($bill);

                // 10. Log hoạt động
                Log::info('Thanh toán hóa đơn thành công', [
                    'bill_id' => $bill->id,
                    'bill_number' => $bill->bill_number,
                    'bill_type' => $isQuickBill ? 'quick' : 'regular',
                    'final_amount' => $finalAmount,
                    'payment_method' => $paymentMethod,
                    'staff_id' => Auth::id(),
                    'auto_print' => $autoPrint
                ]);

                DB::commit();

                // Sửa lại phần này: In bill và redirect về trang tables.index
                if ($autoPrint) {
                    // Chuyển hướng đến trang in bill trước
                    return redirect()->route('admin.bills.print', $bill->id)
                        ->with('success', 'Thanh toán thành công! Đang in hóa đơn...')
                        ->with('redirect_to', route('admin.tables.index')); // Thêm thông tin redirect
                } else {
                    // Nếu không tự động in, về thẳng trang index
                    return redirect()->route('admin.tables.index')
                        ->with('success', 'Thanh toán thành công!');
                }
            });
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Lỗi thanh toán hóa đơn: ' . $e->getMessage(), [
                'bill_id' => $billId,
                'payment_method' => $request->payment_method ?? 'unknown'
            ]);

            return redirect()
                ->route('admin.tables.index')
                ->with('error', 'Lỗi khi thanh toán: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật báo cáo hàng ngày
     */
    private function updateDailyReport(Bill $bill)
    {
        $reportDate = now()->format('Y-m-d');

        $dailyReport = DailyReport::where('report_date', $reportDate)->first();

        if ($dailyReport) {
            $dailyReport->update([
                'total_revenue' => $dailyReport->total_revenue + $bill->final_amount,
                'total_bills' => $dailyReport->total_bills + 1,
                'total_customers' => $dailyReport->total_customers + ($bill->user_id ? 1 : 0),
                'average_bill_value' => ($dailyReport->total_revenue + $bill->final_amount) / ($dailyReport->total_bills + 1)
            ]);
        } else {
            DailyReport::create([
                'report_date' => $reportDate,
                'total_revenue' => $bill->final_amount,
                'total_bills' => 1,
                'total_customers' => $bill->user_id ? 1 : 0,
                'average_bill_value' => $bill->final_amount
            ]);
        }
    }

    /**
     * Tính tiền giờ chơi (phương thức hỗ trợ)
     */
    private function calculateTimeCharge(Bill $bill)
    {
        $totalTimeCost = 0;

        // 1. Tính tiền giờ thường đã kết thúc (bao gồm cả khi chuyển sang combo)
        $endedRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNotNull('end_time')
            ->get();

        foreach ($endedRegularTime as $timeUsage) {
            $totalTimeCost += $timeUsage->total_price ?? 0;
        }

        // 2. Tính tiền giờ thường đang chạy hoặc tạm dừng (nếu không có combo active)
        $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', false)
            ->where('remaining_minutes', '>', 0)
            ->first();

        // Chỉ tính giờ thường nếu không có combo active
        if (!$activeComboTime) {
            $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
                ->whereNull('end_time')
                ->get();

            foreach ($activeRegularTime as $timeUsage) {
                $elapsedMinutes = $this->calculateElapsedMinutes($timeUsage);
                $effectiveMinutes = $elapsedMinutes - ($timeUsage->paused_duration ?? 0);

                // LÀM TRÒN PHÚT: làm tròn lên đến phút
                $roundedMinutes = ceil($effectiveMinutes);
                $timeCost = ($timeUsage->hourly_rate / 60) * max(0, $roundedMinutes);

                $totalTimeCost += $timeCost;
            }
        }

        return $totalTimeCost;
    }

    /**
     * Tính số phút đã trôi qua (phương thức hỗ trợ)
     */
    private function calculateElapsedMinutes(BillTimeUsage $timeUsage): int
    {
        if ($timeUsage->paused_at) {
            return Carbon::parse($timeUsage->start_time)
                ->diffInMinutes(Carbon::createFromTimestamp($timeUsage->paused_at));
        } else {
            return Carbon::parse($timeUsage->start_time)->diffInMinutes(now());
        }
    }
}
