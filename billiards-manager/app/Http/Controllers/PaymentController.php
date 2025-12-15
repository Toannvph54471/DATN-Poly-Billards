<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\BillTimeUsage;
use App\Models\Payment;
use App\Models\Promotion;
use App\Models\DailyReport;
use App\Models\User;
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
            'staff',
            'billDetails.product',
            'billDetails.combo'
        ])->findOrFail($id);

        // Tính toán chi phí giờ chơi
        $timeCost = $this->calculateTimeCharge($bill);

        // Tính tổng tiền sản phẩm và combo
        $productTotal = BillDetail::where('bill_id', $bill->id)
            ->where('is_combo_component', false)
            ->sum('total_price');

        // Lấy danh sách mã giảm giá khả dụng
        $availablePromotions = Promotion::where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->get();

        // Tính tổng tiền tạm tính
        $totalAmount = $timeCost + $productTotal;

        // Lấy thông tin khuyến mãi đã áp dụng (nếu có)
        $appliedPromotion = null;
        $discountAmount = $bill->discount_amount ?? 0;

        // SỬA LẠI: Sử dụng phương thức extractPromotionInfo
        if ($discountAmount > 0) {
            $appliedPromotion = $this->extractPromotionInfo($bill->note);
        }

        $finalAmount = max(0, $totalAmount - $discountAmount);

        return view('admin.payments.payment', compact(
            'bill',
            'timeCost',
            'productTotal',
            'availablePromotions',
            'totalAmount',
            'discountAmount',
            'finalAmount',
            'appliedPromotion'
        ));
    }

    public function showPaymentMultiple(Request $request)
    {
        $billIds = $request->ids;

        if (!$billIds || !is_array($billIds) || count($billIds) == 0) {
            return redirect()->back()->with('error', 'Vui lồng chọn bill trên danh sách');
        }

        // Lấy toàn bộ bill
        $bills = Bill::with([
            'table',
            'user',
            'staff',
            'billDetails.product',
            'billDetails.combo'
        ])->whereIn('id', $billIds)->get();

        if ($bills->count() === 0) {
            return redirect()->back()->with('error', 'Không tìm thấy bill nào');
        }

        // Tạo mảng chi tiết cho từng bill
        $billData = [];

        foreach ($bills as $bill) {
            // Tính giờ chơi
            $timeCost = $this->calculateTimeCharge($bill);

            // Tính tổng sản phẩm
            $productTotal = BillDetail::where('bill_id', $bill->id)
                ->where('is_combo_component', false)
                ->sum('total_price');

            // Lấy khuyến mãi
            $discountAmount = $bill->discount_amount ?? 0;

            $appliedPromotion = null;
            if ($discountAmount > 0) {
                $appliedPromotion = $this->extractPromotionInfo($bill->note);
            }

            $totalAmount = $timeCost + $productTotal;
            $finalAmount = max(0, $totalAmount - $discountAmount);
            // Gom tất cả lại
            $billData[] = [
                'bill' => $bill,
                'timeCost' => $timeCost,
                'productTotal' => $productTotal,
                'discountAmount' => $discountAmount,
                'appliedPromotion' => $appliedPromotion,
                'totalAmount' => $totalAmount,
                'finalAmount' => $finalAmount
            ];
        }

        // Lấy danh sách khuyến mãi (áp dụng chung)
        $availablePromotions = Promotion::where('status', 'active')
            ->where(fn($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
            ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))
            ->get();

        return view('admin.payments.payment-multiple', compact(
            'billData',
            'availablePromotions'
        ));
    }

    /**
     * Trích xuất thông tin khuyến mãi từ note
     */
    private function extractPromotionInfo($note)
    {
        if (!$note) {
            return null;
        }

        // Pattern để trích xuất thông tin khuyến mãi từ note
        // Format: "Mã KM: CODE - Tên khuyến mãi"
        if (preg_match('/Mã KM:\s*(\w+)\s*-\s*(.+?)(?:\s*\||$)/', $note, $matches)) {
            return [
                'code' => trim($matches[1]),
                'name' => trim($matches[2])
            ];
        }

        return null;
    }

    /**
     * Kiểm tra mã giảm giá
     */
    public function checkPromotion(Request $request)
    {
        $request->validate([
            'promotion_code' => 'required|string',
            'bill_id' => 'required|exists:bills,id',
        ]);

        try {
            $bill = Bill::with(['table', 'billDetails.product'])->find($request->bill_id);

            if ($bill->payment_status !== 'Pending') {
                return response()->json([
                    'valid' => false,
                    'message' => 'Không thể áp dụng mã giảm giá cho hóa đơn đã thanh toán'
                ]);
            }

            // Tính tổng tiền hiện tại
            $timeCost = $this->calculateTimeCharge($bill);
            $productTotal = BillDetail::where('bill_id', $bill->id)
                ->where('is_combo_component', false)
                ->sum('total_price');
            $totalAmount = $timeCost + $productTotal;

            $promotion = Promotion::where('promotion_code', $request->promotion_code)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('start_date')
                        ->orWhere('start_date', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                })
                ->first();

            if (!$promotion) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Mã khuyến mãi không tồn tại hoặc đã hết hạn'
                ]);
            }

            // Kiểm tra điều kiện áp dụng
            if (!$this->checkPromotionConditions($promotion, $bill, $totalAmount)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Mã khuyến mãi không đáp ứng điều kiện áp dụng'
                ]);
            }

            // Tính toán discount
            $discountAmount = 0;
            if ($promotion->discount_type === 'percent') {
                $discountAmount = $totalAmount * ($promotion->discount_value / 100);
            } else {
                $discountAmount = min($promotion->discount_value, $totalAmount);
            }

            return response()->json([
                'valid' => true,
                'message' => 'Mã khuyến mãi hợp lệ',
                'discount_amount' => $discountAmount,
                'promotion' => [
                    'name' => $promotion->name,
                    'code' => $promotion->promotion_code,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Lỗi khi kiểm tra mã giảm giá: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Áp dụng mã giảm giá
     */
    public function applyPromotion(Request $request)
    {
        $request->validate([
            'promotion_code' => 'required|string',
            'bill_id' => 'required|exists:bills,id',
        ]);

        try {
            $bill = Bill::with(['table', 'billDetails.product'])->find($request->bill_id);

            if ($bill->payment_status !== 'Pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể áp dụng mã giảm giá cho hóa đơn đã thanh toán'
                ]);
            }

            // Tính tổng tiền hiện tại
            $timeCost = $this->calculateTimeCharge($bill);
            $productTotal = BillDetail::where('bill_id', $bill->id)
                ->where('is_combo_component', false)
                ->sum('total_price');
            $totalAmount = $timeCost + $productTotal;

            $promotion = Promotion::where('promotion_code', $request->promotion_code)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('start_date')
                        ->orWhere('start_date', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                })
                ->first();

            if (!$promotion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã khuyến mãi không tồn tại hoặc đã hết hạn'
                ]);
            }

            // Kiểm tra điều kiện áp dụng
            if (!$this->checkPromotionConditions($promotion, $bill, $totalAmount)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã khuyến mãi không đáp ứng điều kiện áp dụng'
                ]);
            }

            // Tính toán discount
            $discountAmount = 0;
            if ($promotion->discount_type === 'percent') {
                $discountAmount = $totalAmount * ($promotion->discount_value / 100);
            } else {
                $discountAmount = min($promotion->discount_value, $totalAmount);
            }

            // Tạo note mới với thông tin khuyến mãi
            $promotionNote = "Mã KM: {$promotion->promotion_code} - {$promotion->name}";
            $newNote = $promotionNote . ($bill->note ? " | {$bill->note}" : '');

            // Cập nhật bill với discount_amount
            $bill->update([
                'discount_amount' => $discountAmount,
                'final_amount' => $totalAmount - $discountAmount,
                'note' => $newNote
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Áp dụng mã giảm giá thành công',
                'discount_amount' => $discountAmount,
                'final_amount' => $totalAmount - $discountAmount,
                'promotion' => [
                    'name' => $promotion->name,
                    'code' => $promotion->promotion_code,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi áp dụng mã giảm giá: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa mã giảm giá đã áp dụng
     */
    public function removePromotion(Request $request)
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,id',
        ]);

        try {
            $bill = Bill::find($request->bill_id);

            if ($bill->payment_status !== 'Pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thay đổi mã giảm giá cho hóa đơn đã thanh toán'
                ]);
            }

            // Tính lại tổng tiền
            $timeCost = $this->calculateTimeCharge($bill);
            $productTotal = BillDetail::where('bill_id', $bill->id)
                ->where('is_combo_component', false)
                ->sum('total_price');
            $totalAmount = $timeCost + $productTotal;

            // Xóa thông tin khuyến mãi khỏi note
            $newNote = $this->removePromotionInfoFromNote($bill->note);

            // Reset discount về 0
            $bill->update([
                'discount_amount' => 0,
                'final_amount' => $totalAmount,
                'note' => $newNote ?: null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa mã giảm giá',
                'discount_amount' => 0,
                'final_amount' => $totalAmount,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa mã giảm giá: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa thông tin khuyến mãi từ note
     */
    private function removePromotionInfoFromNote($note)
    {
        if (!$note) {
            return null;
        }

        // Xóa phần thông tin khuyến mãi khỏi note
        $cleanedNote = preg_replace('/Mã KM:\s*\w+\s*-\s*[^|]+(\s*\|\s*)?/', '', $note);
        $cleanedNote = trim($cleanedNote, ' |');

        return $cleanedNote ?: null;
    }

    /**
     * Kiểm tra điều kiện áp dụng khuyến mãi
     */
    private function checkPromotionConditions($promotion, $bill, $totalAmount)
    {
        // Kiểm tra thời gian
        $now = now();
        if ($promotion->start_date && $now < $promotion->start_date) {
            return false;
        }
        if ($promotion->end_date && $now > $promotion->end_date) {
            return false;
        }

        // Kiểm tra điều kiện thời gian chơi tối thiểu
        if ($promotion->min_play_minutes) {
            $playMinutes = $this->calculatePlayMinutes($bill);
            if ($playMinutes < $promotion->min_play_minutes) {
                return false;
            }
        }

        // Kiểm tra điều kiện tổng tiền tối thiểu
        if ($promotion->min_order_amount && $totalAmount < $promotion->min_order_amount) {
            return false;
        }

        // Kiểm tra áp dụng cho combo
        if ($promotion->applies_to_combo) {
            $hasCombo = BillDetail::where('bill_id', $bill->id)
                ->whereNotNull('combo_id')
                ->exists();
            if (!$hasCombo) {
                return false;
            }
        }

        // Kiểm tra áp dụng cho time combo
        if ($promotion->applies_to_time_combo) {
            $hasTimeCombo = BillDetail::where('bill_id', $bill->id)
                ->whereHas('combo', function ($query) {
                    $query->where('is_time_combo', true);
                })
                ->exists();
            if (!$hasTimeCombo) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tính số phút chơi
     */
    private function calculatePlayMinutes($bill)
    {
        $timeUsage = BillTimeUsage::where('bill_id', $bill->id)->first();
        if (!$timeUsage) {
            return 0;
        }

        $startTime = Carbon::parse($timeUsage->start_time);
        $endTime = $timeUsage->end_time ? Carbon::parse($timeUsage->end_time) : now();

        $totalMinutes = $startTime->diffInMinutes($endTime);
        $pausedMinutes = $timeUsage->paused_duration ?? 0;

        return max(0, $totalMinutes - $pausedMinutes);
    }

    /**
     * Tính tiền giờ chơi với làm tròn
     */
    private function calculateTimeCharge(Bill $bill)
    {
        $totalTimeCost = 0;

        // 1. Tính tiền giờ thường đã kết thúc
        $endedRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNotNull('end_time')
            ->get();

        foreach ($endedRegularTime as $timeUsage) {
            $totalTimeCost += $timeUsage->total_price ?? 0;
        }

        // 2. Tính tiền giờ thường đang chạy
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->get();

        foreach ($activeRegularTime as $timeUsage) {
            $timeCost = $this->calculateRoundedTimeCost($timeUsage);
            $totalTimeCost += $timeCost;
        }

        return $totalTimeCost;
    }

    /**
     * Tính số phút đã trôi qua
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

    /**
     * Tính tiền giờ với làm tròn
     */
    private function calculateRoundedTimeCost(BillTimeUsage $timeUsage)
    {
        // Lấy cấu hình làm tròn từ table_rate
        $tableRate = $timeUsage->bill->table->tableRate;
        $roundingMinutes = $tableRate->rounding_minutes ?? 15;
        $minChargeMinutes = $tableRate->min_charge_minutes ?? 15;
        $roundingAmount = $tableRate->rounding_amount ?? 1000;

        // Tính số phút đã sử dụng
        $elapsedMinutes = $this->calculateElapsedMinutes($timeUsage);
        $effectiveMinutes = max($minChargeMinutes, $elapsedMinutes - ($timeUsage->paused_duration ?? 0));

        // Làm tròn số phút lên
        $roundedMinutes = ceil($effectiveMinutes / $roundingMinutes) * $roundingMinutes;

        // Tính tiền
        $hourlyRate = $timeUsage->hourly_rate;
        $rawPrice = ($hourlyRate / 60) * $roundedMinutes;
        $finalPrice = ceil($rawPrice / $roundingAmount) * $roundingAmount;

        return $finalPrice;
    }

    /**
     * Xử lý thanh toán - Cập nhật phần tính giờ
     */
    public function processPayment(Request $request, $billId)
    {
        $request->validate([
            'payment_method' => 'required|string|in:cash,bank,card',
            'amount' => 'required|numeric|min:0',
            'promotion_code' => 'nullable|string',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            return DB::transaction(function () use ($request, $billId) {
                $paymentMethod = $request->payment_method;
                $note = $request->note;
                $staffId = Auth::id();

                // 1. Lấy bill
                $bill = Bill::with(['table.tableRate', 'billDetails.product', 'billDetails.combo', 'staff'])
                    ->whereIn('status', ['Open', 'quick'])
                    ->where('payment_status', 'Pending')
                    ->findOrFail($billId);

                $isQuickBill = $bill->status === 'quick';

                // 2. Xử lý thời gian chơi với làm tròn
                $timePrice = 0;
                $totalMinutesPlayed = 0;
                $endTime = now();

                if (!$isQuickBill) {
                    $timeUsage = BillTimeUsage::where('bill_id', $bill->id)
                        ->whereNull('end_time')
                        ->first();

                    if ($timeUsage) {
                        $tableRate = $bill->table->tableRate;
                        $roundingMinutes = $tableRate->rounding_minutes ?? 15;
                        $minChargeMinutes = $tableRate->min_charge_minutes ?? 15;
                        $roundingAmount = $tableRate->rounding_amount ?? 1000;

                        $startTime = Carbon::parse($timeUsage->start_time);
                        $totalMinutesPlayed = $startTime->diffInMinutes($endTime);

                        if ($timeUsage->paused_duration) {
                            $totalMinutesPlayed -= $timeUsage->paused_duration;
                        }

                        // Đảm bảo tối thiểu
                        $totalMinutesPlayed = max($minChargeMinutes, $totalMinutesPlayed);

                        // Làm tròn phút lên
                        $roundedMinutes = ceil($totalMinutesPlayed / $roundingMinutes) * $roundingMinutes;

                        // Tính tiền
                        $hourlyRate = $timeUsage->hourly_rate ?? ($bill->table->tableRate->hourly_rate ?? 0);
                        $rawPrice = ($hourlyRate / 60) * $roundedMinutes;

                        // Làm tròn tiền lên
                        $timePrice = ceil($rawPrice / $roundingAmount) * $roundingAmount;

                        $timeUsage->update([
                            'end_time' => $endTime,
                            'duration_minutes' => $roundedMinutes,
                            'total_price' => $timePrice,
                        ]);
                    } else {
                        $endedTimeUsage = BillTimeUsage::where('bill_id', $bill->id)
                            ->whereNotNull('end_time')
                            ->first();

                        if ($endedTimeUsage) {
                            $timePrice = $endedTimeUsage->total_price ?? 0;
                            $totalMinutesPlayed = $endedTimeUsage->duration_minutes ?? 0;
                        }
                    }
                }

                // 3. Tính tiền sản phẩm
                $productTotal = $bill->billDetails()
                    ->where('is_combo_component', 0)
                    ->sum('total_price');

                // 4. Xử lý khuyến mãi
                $discountAmount = $bill->discount_amount ?? 0;

                // 5. Tổng tiền và final amount
                $totalAmount = $isQuickBill ? $productTotal : ($timePrice + $productTotal);
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

                // 7. Lưu thanh toán
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
                    'processed_by' => $staffId,
                    'note' => $note,
                    'payment_data' => json_encode([
                        'bill_number' => $bill->bill_number,
                        'table' => $bill->table->table_number,
                        'bill_type' => $isQuickBill ? 'quick' : 'regular',
                        'actual_minutes' => $totalMinutesPlayed,
                        'rounded_minutes' => $endedTimeUsage->duration_minutes ?? $totalMinutesPlayed,
                        'hourly_rate' => $bill->table->tableRate->hourly_rate ?? 0,
                        'time_price' => $timePrice,
                        'product_total' => $productTotal,
                        'discount' => $discountAmount,
                        'final_amount' => $finalAmount,
                        'opened_by_staff_id' => $bill->staff_id,
                        'closed_by_staff_id' => $staffId,
                        'opened_by_staff_name' => $bill->staff->name ?? 'N/A',
                        'closed_by_staff_name' => Auth::user()->name,
                    ]),
                ]);

                // 8. Giải phóng bàn
                $bill->table->update(['status' => 'available']);

                // 9. Cập nhật báo cáo hàng ngày
                $this->updateDailyReport($bill);

                DB::commit();

                return redirect()->route('admin.bills.print', [
                    'id' => $bill->id,
                    'auto_print' => 'true',
                    'success' => 'Thanh toán thành công! Nhân viên thanh toán: ' . Auth::user()->name
                ]);
            });
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Lỗi thanh toán hóa đơn: ' . $e->getMessage(), [
                'bill_id' => $billId,
                'payment_method' => $request->payment_method ?? 'unknown',
                'staff_id' => Auth::id(),
                'staff_name' => Auth::user()->name
            ]);

            return redirect()
                ->route('admin.tables.index')
                ->with('error', 'Lỗi khi thanh toán: ' . $e->getMessage());
        }
    }

    /**
     * Xuất hóa đơn
     */
    public function processPaymentMultiple(Request $request)
    {
        $request->validate([
            'bill_ids' => 'required|array|min:1',
            'payment_method' => 'required|string|in:cash,bank,card',
            'amount' => 'required|numeric|min:0',
            'promotion_code' => 'nullable|string',
            'note' => 'nullable|string|max:500',
        ]);

        $billIds = $request->bill_ids;
        $paymentMethod = $request->payment_method;
        $note = $request->note;
        $staffId = Auth::id();

        try {
            DB::transaction(function () use ($billIds, $paymentMethod, $note, $staffId) {
                foreach ($billIds as $billId) {

                    // 1. Lấy bill
                    $bill = Bill::with(['table.tableRate', 'billDetails.product', 'billDetails.combo', 'staff'])
                        ->whereIn('status', ['Open', 'quick'])
                        ->where('payment_status', 'Pending')
                        ->findOrFail($billId);

                    $isQuickBill = $bill->status === 'quick';
                    $endTime = now();

                    // === 2. Xử lý thời gian ===
                    $timePrice = 0;
                    $totalMinutesPlayed = 0;
                    $roundedMinutes = 0;

                    if (!$isQuickBill) {
                        $timeUsage = BillTimeUsage::where('bill_id', $bill->id)
                            ->whereNull('end_time')
                            ->first();

                        if ($timeUsage) {
                            $tableRate = $bill->table->tableRate;
                            $roundingMinutes = $tableRate->rounding_minutes ?? 15;
                            $minChargeMinutes = $tableRate->min_charge_minutes ?? 15;
                            $roundingAmount = $tableRate->rounding_amount ?? 1000;

                            $startTime = Carbon::parse($timeUsage->start_time);
                            $totalMinutesPlayed = $startTime->diffInMinutes($endTime);

                            if ($timeUsage->paused_duration) {
                                $totalMinutesPlayed -= $timeUsage->paused_duration;
                            }

                            $totalMinutesPlayed = max($minChargeMinutes, $totalMinutesPlayed);

                            $roundedMinutes = ceil($totalMinutesPlayed / $roundingMinutes) * $roundingMinutes;

                            $hourlyRate = $timeUsage->hourly_rate
                                ?? ($bill->table->tableRate->hourly_rate ?? 0);
                            $rawPrice = ($hourlyRate / 60) * $roundedMinutes;
                            $timePrice = ceil($rawPrice / $roundingAmount) * $roundingAmount;
                            $timeUsage->update([
                                'end_time' => $endTime,
                                'duration_minutes' => $roundedMinutes,
                                'total_price' => $timePrice,
                            ]);
                        } else {
                            $ended = BillTimeUsage::where('bill_id', $bill->id)
                                ->whereNotNull('end_time')
                                ->first();

                            if ($ended) {
                                $timePrice = $ended->total_price ?? 0;
                                $roundedMinutes = $ended->duration_minutes ?? 0;
                            }
                        }
                    }

                    // === 3. Tính tiền sản phẩm ===
                    $productTotal = $bill->billDetails()
                        ->where('is_combo_component', 0)
                        ->sum('total_price');

                    // === 4. Khuyến mãi ===
                    $discountAmount = $bill->discount_amount ?? 0;

                    // === 5. Tổng tiền ===
                    $totalAmount = $isQuickBill ? $productTotal : ($timePrice + $productTotal);
                    $finalAmount = max(0,  $totalAmount - $discountAmount);

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

                    // === 7. Tạo payment ===
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
                        'processed_by' => $staffId,
                        'note' => $note,
                        'payment_data' => json_encode([
                            'bill_number' => $bill->bill_number,
                            'table' => $bill->table->table_number,
                            'bill_type' => $isQuickBill ? 'quick' : 'regular',
                            'actual_minutes' => $totalMinutesPlayed,
                            'rounded_minutes' => $endedTimeUsage->duration_minutes ?? $totalMinutesPlayed,
                            'hourly_rate' => $bill->table->tableRate->hourly_rate ?? 0,
                            'time_price' => $timePrice,
                            'product_total' => $productTotal,
                            'discount' => $discountAmount,
                            'final_amount' => $finalAmount,
                            'opened_by_staff_id' => $bill->staff_id,
                            'closed_by_staff_id' => $staffId,
                            'opened_by_staff_name' => $bill->staff->name ?? 'N/A',
                            'closed_by_staff_name' => Auth::user()->name,
                        ]),
                    ]);

                    // === 8. Giải phóng bàn ===
                    $bill->table->update(['status' => 'available']);

                    // === 9. Báo cáo ===
                    $this->updateDailyReport($bill);

                    // 9. Cập nhật báo cáo hàng ngày
                    $this->updateDailyReport($bill);
                }
            });

            return redirect()->route('admin.bills.print-multiple', [
                'ids' => $billIds,
                'auto_print' => 'true',
                'success' => 'Thanh toán nhiều hóa đơn thành công! Nhân viên thanh toán: ' . Auth::user()->name
            ]);

            // return redirect()
            //     ->route('admin.bills.index')
            //     ->with('success', 'Thanh toán nhiều hóa đơn thành công!');
        } catch (Exception $e) {
            Log::error('Lỗi thanh toán nhiều bill: ' . $e->getMessage(), [
                'bill_ids' => $billIds,
                'staff_id' => Auth::id(),
            ]);

            return redirect()
                ->route('admin.payments.payment-page-multiple', ['ids' => $billIds])
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
     * Tính toán chi phí giờ chơi chi tiết
     */
    private function calculateTimeChargeDetailed(Bill $bill)
    {
        $totalCost = 0;
        $totalMinutes = 0;
        $hourlyRate = 0;
        $sessions = [];

        $timeUsages = BillTimeUsage::where('bill_id', $bill->id)->get();

        foreach ($timeUsages as $timeUsage) {
            if ($timeUsage->end_time) {
                // Session đã kết thúc
                $sessionMinutes = $timeUsage->duration_minutes ?? 0;
                $sessionCost = $timeUsage->total_price ?? 0;
            } else {
                // Session đang chạy
                $startTime = Carbon::parse($timeUsage->start_time);
                $sessionMinutes = $startTime->diffInMinutes(now());
                $sessionMinutes -= $timeUsage->paused_duration ?? 0;
                $sessionCost = ($timeUsage->hourly_rate / 60) * max(0, $sessionMinutes);
            }

            $totalMinutes += $sessionMinutes;
            $totalCost += $sessionCost;
            $hourlyRate = $timeUsage->hourly_rate;

            $sessions[] = [
                'start_time' => $timeUsage->start_time,
                'end_time' => $timeUsage->end_time,
                'minutes' => $sessionMinutes,
                'cost' => $sessionCost,
                'hourly_rate' => $timeUsage->hourly_rate
            ];
        }

        return [
            'totalCost' => $totalCost,
            'total_minutes' => $totalMinutes,
            'hourly_rate' => $hourlyRate,
            'sessions' => $sessions
        ];
    }

    public function previewPayment(Request $request, $billId)
    {
        $request->validate([
            'payment_method' => 'required|string|in:cash,bank,card',
            'amount' => 'required|numeric|min:0',
            'promotion_code' => 'nullable|string',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            return DB::transaction(function () use ($request, $billId) {
                $paymentMethod = $request->payment_method;
                $note = $request->note;
                $staffId = Auth::id();

                // 1. Lấy bill
                $bill = Bill::with(['table.tableRate', 'billDetails.product', 'billDetails.combo', 'staff'])
                    ->whereIn('status', ['Open', 'quick'])
                    ->where('payment_status', 'Pending')
                    ->findOrFail($billId);

                $isQuickBill = $bill->status === 'quick';
                $endTime = now();

                // 2. Xử lý thời gian chơi với làm tròn
                $timePrice = 0;
                $totalMinutesPlayed = 0;
                $roundedMinutes = 0;

                if (!$isQuickBill) {
                    $timeUsage = BillTimeUsage::where('bill_id', $bill->id)
                        ->whereNull('end_time')
                        ->first();

                    if ($timeUsage) {
                        $tableRate = $bill->table->tableRate;
                        $roundingMinutes = $tableRate->rounding_minutes ?? 15;
                        $minChargeMinutes = $tableRate->min_charge_minutes ?? 15;
                        $roundingAmount = $tableRate->rounding_amount ?? 1000;

                        $startTime = Carbon::parse($timeUsage->start_time);
                        $totalMinutesPlayed = $startTime->diffInMinutes($endTime);

                        if ($timeUsage->paused_duration) {
                            $totalMinutesPlayed -= $timeUsage->paused_duration;
                        }

                        // Đảm bảo tối thiểu
                        $totalMinutesPlayed = max($minChargeMinutes, $totalMinutesPlayed);

                        // Làm tròn phút lên
                        $roundedMinutes = ceil($totalMinutesPlayed / $roundingMinutes) * $roundingMinutes;

                        // Tính tiền
                        $hourlyRate = $timeUsage->hourly_rate ?? ($bill->table->tableRate->hourly_rate ?? 0);
                        $rawPrice = ($hourlyRate / 60) * $roundedMinutes;

                        // Làm tròn tiền lên
                        $timePrice = ceil($rawPrice / $roundingAmount) * $roundingAmount;

                        $timeUsage->update([
                            'end_time' => $endTime,
                            'duration_minutes' => $roundedMinutes,
                            'total_price' => $timePrice,
                        ]);
                    } else {
                        $endedTimeUsage = BillTimeUsage::where('bill_id', $bill->id)
                            ->whereNotNull('end_time')
                            ->first();

                        if ($endedTimeUsage) {
                            $timePrice = $endedTimeUsage->total_price ?? 0;
                            $roundedMinutes = $endedTimeUsage->duration_minutes ?? 0;
                        }
                    }
                }

                // 3. Tính tiền sản phẩm
                $productTotal = $bill->billDetails()
                    ->where('is_combo_component', 0)
                    ->sum('total_price');

                // 4. Xử lý khuyến mãi
                $discountAmount = $bill->discount_amount ?? 0;

                // 5. Tổng tiền và final amount
                $totalAmount = $isQuickBill ? $productTotal : ($timePrice + $productTotal);
                $finalAmount = max(0, $totalAmount - $discountAmount);

                // 6. Lưu tạm thông tin thanh toán vào session
                session([
                    'pending_payment_' . $billId => [
                        'bill_id' => $billId,
                        'payment_method' => $paymentMethod,
                        'total_amount' => $totalAmount,
                        'discount_amount' => $discountAmount,
                        'final_amount' => $finalAmount,
                        'time_price' => $timePrice,
                        'product_total' => $productTotal,
                        'rounded_minutes' => $roundedMinutes,
                        'total_minutes_played' => $totalMinutesPlayed,
                        'note' => $note,
                        'staff_id' => $staffId,
                        'preview_time' => now(),
                        'is_quick_bill' => $isQuickBill
                    ]
                ]);

                // 7. Chuyển đến trang xác nhận thanh toán với option in bill
                return redirect()->route('admin.bills.print', [
                    'id' => $billId,
                    'preview' => 'true',
                    'payment_method' => $paymentMethod,
                    'final_amount' => $finalAmount
                ]);
            });
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xem trước thanh toán: ' . $e->getMessage(), [
                'bill_id' => $billId,
                'staff_id' => Auth::id(),
            ]);

            return redirect()
                ->route('admin.payments.show', $billId)
                ->with('error', 'Lỗi khi xử lý thanh toán: ' . $e->getMessage());
        }
    }

    /**
     * Xác nhận thanh toán thành công (sau khi in bill)
     */
    public function confirmPayment(Request $request, $billId)
    {
        try {
            return DB::transaction(function () use ($request, $billId) {
                $staffId = Auth::id();

                // 1. Lấy thông tin thanh toán tạm từ session
                $paymentData = session('pending_payment_' . $billId);

                if (!$paymentData) {
                    return redirect()
                        ->route('admin.payments.show', $billId)
                        ->with('error', 'Thông tin thanh toán không tồn tại hoặc đã hết hạn');
                }

                // 2. Lấy bill
                $bill = Bill::with(['table.tableRate', 'billDetails.product', 'billDetails.combo', 'staff'])
                    ->whereIn('status', ['Open', 'quick'])
                    ->where('payment_status', 'Pending')
                    ->findOrFail($billId);

                $isQuickBill = $bill->status === 'quick';
                $endTime = now();

                // 3. Cập nhật bill với thông tin thanh toán
                $bill->update([
                    'end_time' => $isQuickBill ? $bill->end_time : $endTime,
                    'total_amount' => $paymentData['total_amount'],
                    'discount_amount' => $paymentData['discount_amount'],
                    'final_amount' => $paymentData['final_amount'],
                    'payment_method' => $paymentData['payment_method'],
                    'payment_status' => 'Paid',
                    'status' => 'Closed',
                    'note' => $paymentData['note'] ?? $bill->note,
                ]);

                // 4. Lưu thanh toán vào database
                $payment = Payment::create([
                    'bill_id' => $bill->id,
                    'amount' => $paymentData['final_amount'],
                    'currency' => 'VND',
                    'payment_method' => $paymentData['payment_method'],
                    'payment_type' => 'full',
                    'status' => 'completed',
                    'transaction_id' => 'BILL_' . $bill->bill_number . '_' . now()->format('YmdHis'),
                    'paid_at' => now(),
                    'completed_at' => now(),
                    'processed_by' => $staffId,
                    'note' => $paymentData['note'],
                    'payment_data' => json_encode([
                        'bill_number' => $bill->bill_number,
                        'table' => $bill->table->table_number,
                        'bill_type' => $isQuickBill ? 'quick' : 'regular',
                        'actual_minutes' => $paymentData['total_minutes_played'] ?? 0,
                        'rounded_minutes' => $paymentData['rounded_minutes'] ?? 0,
                        'hourly_rate' => $bill->table->tableRate->hourly_rate ?? 0,
                        'time_price' => $paymentData['time_price'] ?? 0,
                        'product_total' => $paymentData['product_total'] ?? 0,
                        'discount' => $paymentData['discount_amount'] ?? 0,
                        'final_amount' => $paymentData['final_amount'],
                        'opened_by_staff_id' => $bill->staff_id,
                        'closed_by_staff_id' => $staffId,
                        'opened_by_staff_name' => $bill->staff->name ?? 'N/A',
                        'closed_by_staff_name' => Auth::user()->name,
                        'confirmed_at' => now()->format('Y-m-d H:i:s')
                    ]),
                ]);

                // 5. Giải phóng bàn
                $bill->table->update(['status' => 'available']);

                // 6. Cập nhật báo cáo hàng ngày
                $this->updateDailyReport($bill);

                // 7. Xóa session tạm
                session()->forget('pending_payment_' . $billId);

                // 8. Cập nhật số lần ghé thăm và tổng chi tiêu cho khách hàng
                if ($bill->user_id) {
                    $user = User::find($bill->user_id);
                    if ($user) {
                        $user->increment('total_visits');
                        $user->increment('total_spent', $paymentData['final_amount']);
                        $user->last_visit_date = now();

                        // Cập nhật loại khách hàng dựa trên số lần ghé thăm
                        $this->updateCustomerType($user);
                        $user->save();
                    }
                }

                DB::commit();

                // 9. Redirect với thông báo thành công
                return redirect()
                    ->route('admin.bills.index')
                    ->with('success', 'Thanh toán thành công! Hóa đơn đã được xác nhận.');
            });
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xác nhận thanh toán: ' . $e->getMessage(), [
                'bill_id' => $billId,
                'staff_id' => Auth::id(),
            ]);

            return redirect()
                ->route('admin.payments.show', $billId)
                ->with('error', 'Lỗi khi xác nhận thanh toán: ' . $e->getMessage());
        }
    }

    /**
     * Hủy thanh toán (khách hàng quyết định thanh toán sau)
     */
    public function cancelPayment(Request $request, $billId)
    {
        try {
            // 1. Xóa thông tin thanh toán tạm từ session
            session()->forget('pending_payment_' . $billId);

            // 2. Lấy bill
            $bill = Bill::findOrFail($billId);

            // 3. Nếu đã tính thời gian, cần revert lại
            if ($bill->status !== 'quick') {
                $timeUsage = BillTimeUsage::where('bill_id', $billId)
                    ->whereNotNull('end_time')
                    ->latest()
                    ->first();

                if ($timeUsage) {
                    // Hoàn lại thời gian đã kết thúc
                    $timeUsage->update([
                        'end_time' => null,
                        'total_price' => 0,
                        'duration_minutes' => null
                    ]);
                }
            }

            // 4. Redirect về trang chi tiết bàn
            return redirect()
                ->route('admin.tables.detail', $bill->table_id)
                ->with('info', 'Đã hủy thanh toán. Bàn vẫn đang được sử dụng.');
        } catch (Exception $e) {
            Log::error('Lỗi hủy thanh toán: ' . $e->getMessage(), [
                'bill_id' => $billId,
                'staff_id' => Auth::id(),
            ]);

            return redirect()
                ->route('admin.tables.index')
                ->with('error', 'Lỗi khi hủy thanh toán: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật loại khách hàng dựa trên số lần ghé thăm
     */
    private function updateCustomerType(User $user)
    {
        $visitCount = $user->total_visits;

        if ($visitCount >= 10) {
            $user->customer_type = 'VIP';
        } elseif ($visitCount >= 5) {
            $user->customer_type = 'Regular';
        } elseif ($visitCount >= 1) {
            $user->customer_type = 'Returning';
        } else {
            $user->customer_type = 'New';
        }
    }
}
