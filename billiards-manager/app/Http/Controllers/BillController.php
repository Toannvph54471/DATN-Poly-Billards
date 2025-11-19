<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Table;
use App\Models\User;
use App\Models\Combo;
use App\Models\Product;
use App\Models\ComboTimeUsage;
use App\Models\BillTimeUsage;
use App\Models\BillDetail;
use App\Models\Payment;
use App\Models\TableRate;
use App\Models\Reservation;
use App\Models\DailyReport;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BillController extends Controller
{
    /**
     * Tạo bill tính giờ thường
     */
    public function createBill(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'user_phone' => 'nullable|string',
            'user_name' => 'nullable|string|max:255',
            'guest_count' => 'required|integer|min:1',
            'reservation_id' => 'nullable|exists:reservations,id'
        ]);

        try {
            DB::beginTransaction();

            $table = Table::findOrFail($request->table_id);

            if ($table->status !== 'available') {
                return redirect()->back()->with('error', 'Bàn đang được sử dụng');
            }

            // Tìm hoặc tạo user
            $user = null;
            if ($request->user_phone) {
                $user = User::firstOrCreate(
                    ['phone' => $request->user_phone],
                    [
                        'name' => $request->user_name ?? 'Khách vãng lai',
                        'email' => $request->user_phone . '@customer.com',
                        'password' => bcrypt(Str::random(8)),
                        'role_id' => 4,
                        'status' => 'Active'
                    ]
                );
            }

            // Xử lý reservation nếu có
            $reservation = null;
            if ($request->reservation_id) {
                $reservation = Reservation::find($request->reservation_id);
            }

            // Tạo bill number
            $billNumber = 'BILL' . date('Ymd') . str_pad(Bill::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Lấy hourly rate
            $hourlyRate = $this->getTableHourlyRate($table);

            // Tạo bill
            $bill = Bill::create([
                'bill_number' => $billNumber,
                'table_id' => $table->id,
                'user_id' => $user?->id,
                'reservation_id' => $reservation?->id,
                'staff_id' => Auth::id(),
                'start_time' => now(),
                'status' => 'Open',
                'payment_status' => 'Pending',
                'total_amount' => 0,
                'discount_amount' => 0,
                'final_amount' => 0
            ]);

            // Khởi tạo tính giờ
            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $hourlyRate
            ]);

            // Cập nhật trạng thái bàn
            $table->update(['status' => 'occupied']);

            // Cập nhật reservation nếu có
            if ($reservation) {
                $reservation->update([
                    'status' => 'CheckedIn',
                    'checked_in_at' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('admin.tables.detail', $table->id)
                ->with('success', 'Tạo hóa đơn tính giờ thành công');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Create bill error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi tạo hóa đơn: ' . $e->getMessage());
        }
    }

    /**
     * Tạo bill bàn lẻ (không tính giờ)
     */
    public function createQuickBill(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'user_phone' => 'nullable|string',
            'user_name' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $table = Table::findOrFail($request->table_id);

            if ($table->status !== 'available') {
                return redirect()->back()->with('error', 'Bàn đang được sử dụng');
            }

            // Tìm hoặc tạo user
            $user = null;
            if ($request->user_phone) {
                $user = User::firstOrCreate(
                    ['phone' => $request->user_phone],
                    [
                        'name' => $request->user_name ?? 'Khách vãng lai',
                        'email' => $request->user_phone . '@customer.com',
                        'password' => bcrypt(Str::random(8)),
                        'role_id' => 4,
                        'status' => 'Active'
                    ]
                );
            }

            // Tạo bill number
            $billNumber = 'QUICK' . date('Ymd') . str_pad(Bill::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Tạo bill với status là 'quick'
            $bill = Bill::create([
                'bill_number' => $billNumber,
                'table_id' => $table->id,
                'user_id' => $user?->id,
                'staff_id' => Auth::id(),
                'start_time' => now(),
                'status' => 'quick',
                'payment_status' => 'Pending',
                'total_amount' => 0,
                'discount_amount' => 0,
                'final_amount' => 0
            ]);

            // Cập nhật trạng thái bàn
            $table->update(['status' => 'occupied']);

            DB::commit();

            return redirect()->route('admin.tables.detail', $table->id)
                ->with('success', 'Tạo hóa đơn bàn lẻ thành công');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi tạo hóa đơn: ' . $e->getMessage());
        }
    }

    /**
     * Thêm sản phẩm vào bill
     */
    public function addProductToBill(Request $request, $billId)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);
            $product = Product::findOrFail($request->product_id);

            // Kiểm tra tồn kho
            if ($product->stock_quantity < $request->quantity) {
                return redirect()->back()->with('error', "Sản phẩm {$product->name} không đủ tồn kho. Còn: {$product->stock_quantity}");
            }

            // Thêm sản phẩm vào bill
            BillDetail::create([
                'bill_id' => $bill->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'unit_price' => $product->price,
                'original_price' => $product->price,
                'total_price' => $product->price * $request->quantity,
                'is_combo_component' => false
            ]);

            // Cập nhật tồn kho
            $product->decrement('stock_quantity', $request->quantity);

            // Cập nhật tổng tiền
            $this->calculateBillTotal($bill);

            DB::commit();

            return redirect()->back()->with('success', 'Thêm sản phẩm thành công');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi thêm sản phẩm: ' . $e->getMessage());
        }
    }

    /**
     * Thêm combo vào bill
     */
    public function addComboToBill(Request $request, $billId)
    {
        $request->validate([
            'combo_id' => 'required|exists:combos,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);

            // Kiểm tra nếu là bàn lẻ thì không cho thêm combo
            if ($bill->status === 'quick') {
                return redirect()->back()->with('error', 'Bàn lẻ không thể thêm combo');
            }

            $combo = Combo::with('comboItems.product')->findOrFail($request->combo_id);

            // Kiểm tra tồn kho cho tất cả sản phẩm trong combo
            foreach ($combo->comboItems as $item) {
                if ($item->product && $item->product->stock_quantity < ($item->quantity * $request->quantity)) {
                    return redirect()->back()->with(
                        'error',
                        "{$item->product->name} không đủ tồn kho. Cần: " . ($item->quantity * $request->quantity)
                    );
                }
            }

            // Thêm combo vào bill details
            $comboDetail = BillDetail::create([
                'bill_id' => $bill->id,
                'combo_id' => $combo->id,
                'quantity' => $request->quantity,
                'unit_price' => $combo->price,
                'original_price' => $combo->actual_value,
                'total_price' => $combo->price * $request->quantity,
                'is_combo_component' => false
            ]);

            // Xử lý các sản phẩm trong combo
            foreach ($combo->comboItems as $item) {
                if ($item->product_id) {
                    BillDetail::create([
                        'bill_id' => $bill->id,
                        'product_id' => $item->product_id,
                        'parent_bill_detail_id' => $comboDetail->id,
                        'quantity' => $item->quantity * $request->quantity,
                        'unit_price' => 0,
                        'original_price' => $item->product->price,
                        'total_price' => 0,
                        'is_combo_component' => true
                    ]);

                    // Cập nhật tồn kho
                    $item->product->decrement('stock_quantity', $item->quantity * $request->quantity);
                }
            }

            // Xử lý combo có giờ chơi
            if ($combo->is_time_combo && $combo->play_duration_minutes) {
                $this->activateComboTime($bill, $combo, $comboDetail);
            }

            // Cập nhật tổng tiền
            $this->calculateBillTotal($bill);

            DB::commit();

            return redirect()->back()->with('success', 'Thêm combo thành công');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi thêm combo: ' . $e->getMessage());
        }
    }

    /**
     * Tạm dừng tính giờ - CHỈ ÁP DỤNG CHO COMBO TIME
     */
    public function pauseTime($billId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::with('table')->findOrFail($billId);

            // Kiểm tra trạng thái bill
            if ($bill->status !== 'Open') {
                return redirect()->back()->with('error', 'Chỉ có thể tạm dừng bill đang mở');
            }

            // CHỈ cho phép pause khi đang dùng combo time
            $activeComboTime = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', false)
                ->where('remaining_minutes', '>', 0)
                ->whereNull('end_time')
                ->first();

            if (!$activeComboTime) {
                return redirect()->back()->with('error', 'Chỉ có thể tạm dừng khi đang sử dụng combo thời gian');
            }

            // Tính thời gian đã chạy
            $start = Carbon::parse($activeComboTime->start_time);
            $elapsedMinutes = $start->diffInMinutes(now());

            // Cập nhật thời gian pause cho combo
            $activeComboTime->update([
                'end_time' => now(),
                'remaining_minutes' => max(0, $activeComboTime->remaining_minutes - $elapsedMinutes)
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Đã tạm dừng combo thời gian');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error pausing combo time: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi tạm dừng: ' . $e->getMessage());
        }
    }

    /**
     * Tiếp tục tính giờ - CHỈ ÁP DỤNG CHO COMBO TIME
     */
    public function resumeTime($billId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::with('table')->findOrFail($billId);

            // Kiểm tra trạng thái bill
            if ($bill->status !== 'Open') {
                return redirect()->back()->with('error', 'Chỉ có thể tiếp tục bill đang mở');
            }

            // CHỈ cho phép resume combo time đang pause
            $pausedComboTime = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', false)
                ->where('remaining_minutes', '>', 0)
                ->whereNotNull('end_time')
                ->first();

            if (!$pausedComboTime) {
                return redirect()->back()->with('error', 'Không tìm thấy combo thời gian đang tạm dừng');
            }

            // Tiếp tục combo time
            $pausedComboTime->update([
                'start_time' => now(),
                'end_time' => null
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Đã tiếp tục combo thời gian');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resuming combo time: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi tiếp tục: ' . $e->getMessage());
        }
    }

    /**
     * Chuyển từ combo time sang tính giờ thường - STAFF THAO TÁC THỦ CÔNG
     */
    public function switchToRegularTime(Request $request, $billId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);

            // Kiểm tra xem có combo time đã hết hạn không
            $expiredComboTime = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', true)
                ->where('remaining_minutes', 0)
                ->first();

            if (!$expiredComboTime) {
                return redirect()->back()->with('error', 'Combo thời gian chưa kết thúc hoặc không tìm thấy');
            }

            // Kiểm tra xem đã có giờ thường đang chạy chưa
            $existingRegularTime = BillTimeUsage::where('bill_id', $billId)
                ->whereNull('end_time')
                ->first();

            if ($existingRegularTime) {
                return redirect()->back()->with('error', 'Đã có giờ thường đang chạy');
            }

            // Bắt đầu tính giờ thường
            $hourlyRate = $this->getTableHourlyRate($bill->table);
            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $hourlyRate
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Đã chuyển sang tính giờ thường thành công');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi chuyển sang giờ thường: ' . $e->getMessage());
        }
    }

    /**
     * Bắt đầu tính giờ từ bàn lẻ
     */
    public function startPlaying($billId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);

            // Chỉ cho phép nếu bill đang là quick
            if ($bill->status !== 'quick') {
                return redirect()->back()->with('error', 'Chỉ có thể bắt đầu tính giờ từ bàn lẻ');
            }

            // Chuyển từ quick sang Open
            $bill->update([
                'status' => 'Open'
            ]);

            // Bắt đầu tính giờ thường
            $hourlyRate = $this->getTableHourlyRate($bill->table);
            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $hourlyRate
            ]);

            $this->calculateBillTotal($bill);

            DB::commit();

            return redirect()->back()->with('success', 'Đã bắt đầu tính giờ chơi');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật tổng tiền bill
     */
    public function updateBillTotal($billId)
    {
        try {
            $bill = Bill::findOrFail($billId);
            $this->calculateBillTotal($bill);
            $bill->refresh();

            return redirect()->back()->with('success', 'Đã cập nhật tổng tiền');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi cập nhật tổng tiền: ' . $e->getMessage());
        }
    }

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

        return view('admin.bills.payment', compact('bill', 'timeCost', 'productTotal'));
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

                // LUÔN tự động in bill sau khi thanh toán
                return redirect()->route('admin.bills.print', $bill->id)
                    ->with('success', 'Thanh toán thành công! Đang in hóa đơn...');
            });
        } catch (Exception $e) {
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
     * Kích hoạt combo time
     */
    private function activateComboTime(Bill $bill, Combo $combo, BillDetail $comboDetail)
    {
        // Tính toán và lưu giá trị giờ thường đã sử dụng trước khi chuyển sang combo
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeRegularTime) {
            // Tính thời gian đã sử dụng giờ thường
            $elapsedMinutes = $this->calculateElapsedMinutes($activeRegularTime);
            $effectiveMinutes = $elapsedMinutes - ($activeRegularTime->paused_duration ?? 0);
            $regularTimeCost = ($activeRegularTime->hourly_rate / 60) * max(0, $effectiveMinutes);

            // Kết thúc session giờ thường và lưu giá trị
            $activeRegularTime->update([
                'end_time' => now(),
                'duration_minutes' => $elapsedMinutes,
                'total_price' => $regularTimeCost
            ]);
        }

        // Tạo bản ghi theo dõi thời gian combo
        ComboTimeUsage::create([
            'combo_id' => $combo->id,
            'bill_id' => $bill->id,
            'table_id' => $bill->table_id,
            'start_time' => now(),
            'total_minutes' => $combo->play_duration_minutes,
            'remaining_minutes' => $combo->play_duration_minutes,
            'is_expired' => false
        ]);
    }

    /**
     * Tính tổng tiền bill
     */
    public function calculateBillTotal(Bill $bill)
    {
        // Tính tiền sản phẩm (không bao gồm thành phần combo)
        $productTotal = BillDetail::where('bill_id', $bill->id)
            ->where('is_combo_component', false)
            ->sum('total_price');

        // Tính tiền giờ - bao gồm cả đã kết thúc và đang chạy
        $timeTotal = $this->calculateTimeCharge($bill);

        $totalAmount = $productTotal + $timeTotal;
        $finalAmount = $totalAmount - $bill->discount_amount;

        $bill->update([
            'total_amount' => $totalAmount,
            'final_amount' => $finalAmount
        ]);

        return $totalAmount;
    }

    /**
     * Tính tiền giờ chơi
     */
    public function calculateTimeCharge(Bill $bill)
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
                $timeCost = ($timeUsage->hourly_rate / 60) * max(0, $effectiveMinutes);
                $totalTimeCost += $timeCost;
            }
        }

        return $totalTimeCost;
    }

    /**
     * Tự động dừng combo khi hết thời gian - CHỈ DỪNG, KHÔNG TỰ CHUYỂN SANG GIỜ THƯỜNG
     */
    public function autoStopExpiredCombos()
    {
        try {
            DB::beginTransaction();

            // Tìm các combo time đang chạy và đã hết thời gian
            $expiredCombos = ComboTimeUsage::where('is_expired', false)
                ->whereNull('end_time')
                ->where('remaining_minutes', '>', 0)
                ->get();

            $stoppedCount = 0;

            foreach ($expiredCombos as $comboTime) {
                $start = Carbon::parse($comboTime->start_time);
                $elapsedMinutes = $start->diffInMinutes(now());
                $remainingMinutes = max(0, $comboTime->remaining_minutes - $elapsedMinutes);

                // Nếu hết thời gian
                if ($remainingMinutes <= 0) {
                    $comboTime->update([
                        'end_time' => now(),
                        'remaining_minutes' => 0,
                        'is_expired' => true
                    ]);

                    $stoppedCount++;

                    Log::info("Auto stopped combo for bill: {$comboTime->bill_id}, remaining: {$remainingMinutes} minutes");
                }
            }

            DB::commit();

            Log::info("Auto stopped {$stoppedCount} expired combos");
            return $stoppedCount;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error auto stopping combos: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Kiểm tra và cập nhật trạng thái combo time real-time - CHỈ DỪNG, KHÔNG TỰ CHUYỂN
     */
    public function checkComboTimeStatus($billId)
    {
        try {
            $bill = Bill::findOrFail($billId);

            $activeComboTime = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', false)
                ->where('remaining_minutes', '>', 0)
                ->whereNull('end_time')
                ->first();

            if (!$activeComboTime) {
                return [
                    'has_active_combo' => false,
                    'is_near_end' => false,
                    'is_expired' => false,
                    'needs_switch' => false
                ];
            }

            $start = Carbon::parse($activeComboTime->start_time);
            $elapsedMinutes = $start->diffInMinutes(now());
            $remainingMinutes = max(0, $activeComboTime->remaining_minutes - $elapsedMinutes);

            $isNearEnd = $remainingMinutes <= 10 && $remainingMinutes > 0;
            $isExpired = $remainingMinutes <= 0;

            // Tự động dừng nếu hết thời gian, nhưng KHÔNG tự chuyển sang giờ thường
            if ($isExpired) {
                $activeComboTime->update([
                    'end_time' => now(),
                    'remaining_minutes' => 0,
                    'is_expired' => true
                ]);

                return [
                    'has_active_combo' => false,
                    'is_near_end' => false,
                    'is_expired' => true,
                    'needs_switch' => true, // Cần staff chuyển sang giờ thường thủ công
                    'remaining_minutes' => 0,
                    'elapsed_minutes' => $elapsedMinutes
                ];
            }

            return [
                'has_active_combo' => true,
                'is_near_end' => $isNearEnd,
                'is_expired' => false,
                'needs_switch' => false,
                'remaining_minutes' => $remainingMinutes,
                'elapsed_minutes' => $elapsedMinutes
            ];
        } catch (\Exception $e) {
            Log::error('Error checking combo time: ' . $e->getMessage());
            return [
                'has_active_combo' => false,
                'is_near_end' => false,
                'is_expired' => false,
                'needs_switch' => false
            ];
        }
    }

    /**
     * Dừng tất cả tính giờ
     */
    private function stopAllTimeUsage(Bill $bill)
    {
        // Dừng regular time chưa kết thúc
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->get();

        foreach ($activeRegularTime as $timeUsage) {
            $elapsedMinutes = $this->calculateElapsedMinutes($timeUsage);
            $effectiveMinutes = $elapsedMinutes - ($timeUsage->paused_duration ?? 0);

            $timeUsage->update([
                'end_time' => now(),
                'duration_minutes' => $elapsedMinutes,
                'total_price' => ($timeUsage->hourly_rate / 60) * max(0, $effectiveMinutes)
            ]);
        }

        // Dừng combo time chưa kết thúc
        $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', false)
            ->whereNull('end_time')
            ->get();

        foreach ($activeComboTime as $comboTime) {
            $elapsedMinutes = Carbon::parse($comboTime->start_time)->diffInMinutes(now());

            $comboTime->update([
                'end_time' => now(),
                'total_minutes' => $comboTime->total_minutes,
                'remaining_minutes' => max(0, $comboTime->total_minutes - $elapsedMinutes),
                'is_expired' => true
            ]);
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
     * Lấy giá giờ của bàn
     */
    private function getTableHourlyRate(Table $table)
    {
        if ($table->table_rate_id) {
            $tableRate = TableRate::find($table->table_rate_id);
            if ($tableRate) {
                return $tableRate->hourly_rate;
            }
        }

        // Default rate nếu không tìm thấy
        return 50000.00;
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

    public function showTransferForm($billId)
    {
        try {
            $bill = Bill::with(['table', 'user'])
                ->where('status', 'Open')
                ->where('payment_status', 'Pending')
                ->findOrFail($billId);

            $availableTables = Table::where('status', 'available')
                ->where('id', '!=', $bill->table_id)
                ->get();

            return view('admin.bills.transfer', compact('bill', 'availableTables'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.tables.index')
                ->with('error', 'Không tìm thấy bill hoặc bill không hợp lệ');
        }
    }

    /**
     * Xử lý chuyển bàn
     */
    public function transferTable(Request $request)
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'target_table_id' => 'required|exists:tables,id'
        ]);

        try {
            return DB::transaction(function () use ($request) {

                // 1. Kiểm tra bill và bàn
                $bill = Bill::with(['table', 'billDetails', 'billTimeUsages'])
                    ->where('status', 'Open')
                    ->where('payment_status', 'Pending')
                    ->findOrFail($request->bill_id);

                $sourceTable = $bill->table;
                $targetTable = Table::findOrFail($request->target_table_id);

                // 2. Kiểm tra bàn đích có trống không
                if ($targetTable->status !== 'available') {
                    throw new \Exception('Bàn đích đang được sử dụng hoặc bảo trì');
                }

                // 3. Kiểm tra không chuyển cùng bàn
                if ($sourceTable->id === $targetTable->id) {
                    throw new \Exception('Không thể chuyển cùng một bàn');
                }

                // 4. Cập nhật bill sang bàn mới
                $bill->update([
                    'table_id' => $targetTable->id,
                    'note' => $bill->note . " [Chuyển từ bàn {$sourceTable->table_number} lúc " . now()->format('H:i d/m/Y') . "]"
                ]);

                // 5. Cập nhật combo time usage nếu có
                ComboTimeUsage::where('bill_id', $bill->id)
                    ->where('table_id', $sourceTable->id)
                    ->update(['table_id' => $targetTable->id]);

                // 6. Cập nhật trạng thái bàn
                $sourceTable->update(['status' => 'available']);
                $targetTable->update(['status' => 'occupied']);

                // 7. Log hoạt động
                Log::info('Chuyển bàn thành công', [
                    'bill_id' => $bill->id,
                    'bill_number' => $bill->bill_number,
                    'source_table' => $sourceTable->table_number,
                    'target_table' => $targetTable->table_number,
                    'staff_id' => Auth::id()
                ]);

                return redirect()
                    ->route('admin.tables.index')
                    ->with('success', "Đã chuyển bàn {$sourceTable->table_number} → {$targetTable->table_number} thành công");
            });
        } catch (\Exception $e) {
            Log::error('Lỗi chuyển bàn', [
                'bill_id' => $request->bill_id,
                'target_table' => $request->target_table_id,
                'error' => $e->getMessage(),
                'staff_id' => Auth::id()
            ]);

            return redirect()
                ->route('admin.tables.index')
                ->with('error', 'Lỗi khi chuyển bàn: ' . $e->getMessage());
        }
    }

    /**
     * API lấy danh sách bàn trống (cho AJAX)
     */
    public function getAvailableTables($billId)
    {
        try {
            $bill = Bill::with('table')->findOrFail($billId);

            $availableTables = Table::where('status', 'available')
                ->where('id', '!=', $bill->table_id)
                ->get(['id', 'table_number', 'table_name', 'table_rate_id']);

            return response()->json([
                'success' => true,
                'current_table' => [
                    'id' => $bill->table->id,
                    'table_number' => $bill->table->table_number,
                    'table_name' => $bill->table->table_name
                ],
                'available_tables' => $availableTables
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * In hóa đơn
     */
    public function printBill($id)
    {
        try {
            $bill = Bill::with([
                'table',
                'user',
                'billDetails.product',
                'billDetails.combo',
                'billTimeUsages',
                'comboTimeUsages.combo'
            ])->findOrFail($id);

            // Tính toán chi phí với thông tin chi tiết
            $timeDetails = $this->calculateTimeChargeDetailed($bill);
            $timeCost = $timeDetails['totalCost'];

            $productTotal = BillDetail::where('bill_id', $bill->id)
                ->where('is_combo_component', false)
                ->sum('total_price');

            $totalAmount = $timeCost + $productTotal;
            $finalAmount = $totalAmount - $bill->discount_amount;

            // Dữ liệu cho bill
            $billData = [
                'bill' => $bill,
                'timeCost' => $timeCost,
                'timeDetails' => $timeDetails, // Thêm chi tiết giờ
                'productTotal' => $productTotal,
                'totalAmount' => $totalAmount,
                'finalAmount' => $finalAmount,
                'printTime' => now()->format('H:i d/m/Y'),
                'staff' => Auth::user()->name
            ];

            return view('admin.bills.print', $billData);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi in hóa đơn: ' . $e->getMessage());
        }
    }

    public function calculateTimeChargeDetailed(Bill $bill)
    {
        $timeDetails = [
            'totalCost' => 0,
            'totalMinutes' => 0,
            'sessions' => [],
            'hourlyRate' => 0
        ];

        // 1. Tính tiền giờ thường đã kết thúc
        $endedRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNotNull('end_time')
            ->get();

        foreach ($endedRegularTime as $timeUsage) {
            $sessionCost = $timeUsage->total_price ?? 0;
            $sessionMinutes = $timeUsage->duration_minutes ?? 0;

            $timeDetails['sessions'][] = [
                'type' => 'regular_ended',
                'minutes' => $sessionMinutes,
                'hours' => round($sessionMinutes / 60, 2),
                'hourly_rate' => $timeUsage->hourly_rate,
                'cost' => $sessionCost,
                'description' => "Giờ thường: " . $this->formatDuration($sessionMinutes),
                'calculation' => $this->formatTimeCalculation($timeUsage->hourly_rate, $sessionMinutes, $sessionCost)
            ];

            $timeDetails['totalCost'] += $sessionCost;
            $timeDetails['totalMinutes'] += $sessionMinutes;

            if ($timeUsage->hourly_rate > 0) {
                $timeDetails['hourlyRate'] = $timeUsage->hourly_rate;
            }
        }

        // 2. Tính tiền giờ thường đang chạy
        $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', false)
            ->where('remaining_minutes', '>', 0)
            ->first();

        if (!$activeComboTime) {
            $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
                ->whereNull('end_time')
                ->get();

            foreach ($activeRegularTime as $timeUsage) {
                $elapsedMinutes = $this->calculateElapsedMinutes($timeUsage);
                $effectiveMinutes = $elapsedMinutes - ($timeUsage->paused_duration ?? 0);
                $sessionCost = ($timeUsage->hourly_rate / 60) * max(0, $effectiveMinutes);

                $timeDetails['sessions'][] = [
                    'type' => 'regular_active',
                    'minutes' => $effectiveMinutes,
                    'hours' => round($effectiveMinutes / 60, 2),
                    'hourly_rate' => $timeUsage->hourly_rate,
                    'cost' => $sessionCost,
                    'description' => "Giờ đang chạy: " . $this->formatDuration($effectiveMinutes),
                    'calculation' => $this->formatTimeCalculation($timeUsage->hourly_rate, $effectiveMinutes, $sessionCost)
                ];

                $timeDetails['totalCost'] += $sessionCost;
                $timeDetails['totalMinutes'] += $effectiveMinutes;
                $timeDetails['hourlyRate'] = $timeUsage->hourly_rate;
            }
        }

        return $timeDetails;
    }

    /**
     * Format thời gian từ phút sang "XhYp"
     */
    private function formatDuration($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours}h{$mins}p";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$mins}p";
        }
    }

    /**
     * Format công thức tính tiền giờ
     */
    private function formatTimeCalculation($hourlyRate, $minutes, $cost)
    {
        $hours = $minutes / 60;
        $hourlyRateFormatted = number_format($hourlyRate, 0, ',', '.');
        $costFormatted = number_format($cost, 0, ',', '.');

        return "{$hourlyRateFormatted}₫/h × {$hours}h = {$costFormatted}₫";
    }
}
