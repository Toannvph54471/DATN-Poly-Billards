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
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BillController extends Controller
{
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

    public function switchToRegularTime(Request $request, $billId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);

            // Kiểm tra xem có đang dùng combo time không
            $activeComboTime = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', false)
                ->where('remaining_minutes', '>', 0)
                ->first();

            if (!$activeComboTime) {
                return redirect()->back()->with('error', 'Không tìm thấy combo thời gian đang hoạt động');
            }

            // Đánh dấu combo time đã hết hạn
            $activeComboTime->update([
                'end_time' => now(),
                'is_expired' => true,
                'remaining_minutes' => 0
            ]);

            // Bắt đầu tính giờ thường
            $hourlyRate = $this->getTableHourlyRate($bill->table);
            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $hourlyRate
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Đã chuyển sang tính giờ thường');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi chuyển sang giờ thường: ' . $e->getMessage());
        }
    }

    public function extendComboTime(Request $request, $billId)
    {
        $request->validate([
            'extra_minutes' => 'required|integer|min:15'
        ]);

        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);
            $comboTimeUsage = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', false)
                ->first();

            if (!$comboTimeUsage) {
                return redirect()->back()->with('error', 'Không tìm thấy combo thời gian đang hoạt động');
            }

            // Tính phí phát sinh
            $hourlyRate = $this->getTableHourlyRate($bill->table);
            $extraCharge = ($hourlyRate / 60) * $request->extra_minutes;

            // Cập nhật thời gian
            $comboTimeUsage->update([
                'remaining_minutes' => $comboTimeUsage->remaining_minutes + $request->extra_minutes
            ]);

            // Thêm phí phát sinh vào bill details
            BillDetail::create([
                'bill_id' => $bill->id,
                'quantity' => 1,
                'unit_price' => $extraCharge,
                'original_price' => $extraCharge,
                'total_price' => $extraCharge,
                'note' => "Phí gia hạn thêm {$request->extra_minutes} phút"
            ]);

            $this->calculateBillTotal($bill);

            DB::commit();

            return redirect()->back()->with('success', "Đã gia hạn thêm {$request->extra_minutes} phút");
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi gia hạn thời gian: ' . $e->getMessage());
        }
    }

    public function pauseTime($billId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);

            // Dừng giờ thường nếu đang chạy
            $activeRegularTime = BillTimeUsage::where('bill_id', $billId)
                ->whereNull('end_time')
                ->whereNull('paused_at')
                ->first();

            if ($activeRegularTime) {
                $elapsedMinutes = $activeRegularTime->start_time->diffInMinutes(now());
                $activeRegularTime->update([
                    'paused_at' => now()->timestamp,
                    'duration_minutes' => $elapsedMinutes
                ]);
            }

            // Dừng combo time nếu đang chạy
            $activeComboTime = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', false)
                ->whereNull('end_time')
                ->first();

            if ($activeComboTime) {
                $elapsedMinutes = $activeComboTime->start_time->diffInMinutes(now());
                $remainingMinutes = max(0, $activeComboTime->remaining_minutes - $elapsedMinutes);

                $activeComboTime->update([
                    'end_time' => now(),
                    'remaining_minutes' => $remainingMinutes
                ]);
            }

            $this->calculateBillTotal($bill);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Đã tạm dừng tính giờ']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi khi tạm dừng: ' . $e->getMessage()]);
        }
    }

    public function resumeTime($billId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);

            // Resume regular time
            $pausedRegularTime = BillTimeUsage::where('bill_id', $billId)
                ->whereNotNull('paused_at')
                ->whereNull('end_time')
                ->first();

            if ($pausedRegularTime) {
                $pauseDuration = (now()->timestamp - $pausedRegularTime->paused_at) / 60;

                $pausedRegularTime->update([
                    'paused_duration' => ($pausedRegularTime->paused_duration ?? 0) + $pauseDuration,
                    'paused_at' => null,
                    'start_time' => now()
                ]);
            }

            // Resume combo time
            $pausedComboTime = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', false)
                ->whereNotNull('end_time')
                ->where('remaining_minutes', '>', 0)
                ->first();

            if ($pausedComboTime) {
                $pausedComboTime->update([
                    'start_time' => now(),
                    'end_time' => null
                ]);
            }

            // Tạo regular time mới nếu không có time nào active
            if (!$pausedRegularTime && !$pausedComboTime) {
                $activeTimeExists = BillTimeUsage::where('bill_id', $billId)
                    ->whereNull('end_time')
                    ->exists();

                if (!$activeTimeExists) {
                    $hourlyRate = $this->getTableHourlyRate($bill->table);
                    BillTimeUsage::create([
                        'bill_id' => $bill->id,
                        'start_time' => now(),
                        'hourly_rate' => $hourlyRate
                    ]);
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Đã tiếp tục tính giờ']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi khi tiếp tục: ' . $e->getMessage()]);
        }
    }

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

    public function showPayment($id)
    {
        $bill = Bill::with([
            'table',
            'user',
            'billDetails.product',
            'billDetails.combo',
            'billTimeUsages',
            'comboTimeUsages'
        ])->findOrFail($id);

        // Tính toán chi phí giờ chơi
        $timeCost = $this->calculateTimeCharge($bill);

        // Tính tổng tiền sản phẩm và combo
        $productTotal = BillDetail::where('bill_id', $bill->id)
            ->where('is_combo_component', false)
            ->sum('total_price');

        // Thông tin thời gian
        $timeDetails = [
            'total_minutes' => 0,
            'hourly_rate' => $this->getTableHourlyRate($bill->table)
        ];

        // Tính tổng số phút đã chơi
        $regularMinutes = BillTimeUsage::where('bill_id', $bill->id)->sum('duration_minutes');
        $comboMinutes = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', true)
            ->sum('total_minutes');

        $timeDetails['total_minutes'] = $regularMinutes + $comboMinutes;

        return view('admin.bills.payment', compact('bill', 'timeCost', 'productTotal', 'timeDetails'));
    }

    public function processPayment(Request $request, $billId)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,bank,card',
            'amount' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);

            // Dừng tất cả tính giờ
            $this->stopAllTimeUsage($bill);

            // Tính toán lại tổng tiền cuối cùng
            $this->calculateBillTotal($bill);

            // Tạo bản ghi thanh toán
            Payment::create([
                'bill_id' => $bill->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            // Cập nhật trạng thái bill
            $bill->update([
                'payment_status' => 'Paid',
                'end_time' => now(),
                'status' => 'Closed',
                'payment_method' => $request->payment_method,
                'final_amount' => $request->amount
            ]);

            // Giải phóng bàn
            $bill->table->update(['status' => 'available']);

            // Cập nhật thông tin user (nếu có)
            if ($bill->user) {
                $bill->user->increment('total_visits');
                $bill->user->increment('total_spent', $bill->final_amount);

                // Cập nhật customer_type dựa trên số lần visit
                $visitCount = $bill->user->total_visits;
                if ($visitCount >= 10) {
                    $bill->user->update(['customer_type' => 'VIP']);
                } elseif ($visitCount >= 5) {
                    $bill->user->update(['customer_type' => 'Regular']);
                }
            }

            DB::commit();

            return redirect()->route('admin.tables.index')
                ->with('success', 'Thanh toán thành công. Hóa đơn: ' . $bill->bill_number);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi thanh toán: ' . $e->getMessage());
        }
    }

    public function getTableTimeInfo($tableId)
    {
        try {
            $table = Table::with([
                'currentBill.billTimeUsages',
                'currentBill.comboTimeUsages'
            ])->findOrFail($tableId);

            $timeInfo = $this->calculateTableTimeInfo($table);

            return response()->json([
                'success' => true,
                'data' => $timeInfo
            ]);
        } catch (Exception $e) {
            Log::error("Get table time info error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống'
            ], 500);
        }
    }

    // ========== PRIVATE METHODS ==========

    private function activateComboTime(Bill $bill, Combo $combo, BillDetail $comboDetail)
    {
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

        // Tạm dừng tính giờ thường (nếu có)
        $activeTimeUsage = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeTimeUsage) {
            $elapsedMinutes = $activeTimeUsage->start_time->diffInMinutes(now());
            $activeTimeUsage->update([
                'end_time' => now(),
                'duration_minutes' => $elapsedMinutes,
                'total_price' => ($activeTimeUsage->hourly_rate / 60) * $elapsedMinutes
            ]);
        }
    }

    public function calculateBillTotal(Bill $bill)
    {
        // Tính tiền sản phẩm (không bao gồm thành phần combo)
        $productTotal = BillDetail::where('bill_id', $bill->id)
            ->where('is_combo_component', false)
            ->sum('total_price');

        // Tính tiền giờ - chỉ tính khi status là 'Open'
        $timeTotal = 0;
        if ($bill->status === 'Open') {
            $timeTotal = $this->calculateTimeCharge($bill);
        }

        $totalAmount = $productTotal + $timeTotal;
        $finalAmount = $totalAmount - $bill->discount_amount;

        $bill->update([
            'total_amount' => $totalAmount,
            'final_amount' => $finalAmount
        ]);

        return $totalAmount;
    }

    public function calculateTimeCharge(Bill $bill)
    {
        $totalTimeCost = 0;

        // 1. Tính tiền giờ thường đã kết thúc
        $endedRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNotNull('end_time')
            ->get();

        foreach ($endedRegularTime as $timeUsage) {
            $effectiveMinutes = $timeUsage->duration_minutes - ($timeUsage->paused_duration ?? 0);
            $totalTimeCost += ($timeUsage->hourly_rate / 60) * max(0, $effectiveMinutes);
        }

        // 2. Tính tiền giờ thường đang chạy hoặc tạm dừng
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeRegularTime) {
            $elapsedMinutes = $activeRegularTime->getElapsedMinutes();
            $effectiveMinutes = $elapsedMinutes - ($activeRegularTime->paused_duration ?? 0);
            $totalTimeCost += ($activeRegularTime->hourly_rate / 60) * max(0, $effectiveMinutes);
        }

        return $totalTimeCost;
    }

    private function stopAllTimeUsage(Bill $bill)
    {
        // Dừng regular time
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeRegularTime) {
            $elapsedMinutes = $activeRegularTime->getElapsedMinutes();
            $effectiveMinutes = $elapsedMinutes - ($activeRegularTime->paused_duration ?? 0);

            $activeRegularTime->update([
                'end_time' => now(),
                'duration_minutes' => $elapsedMinutes,
                'total_price' => ($activeRegularTime->hourly_rate / 60) * max(0, $effectiveMinutes)
            ]);
        }

        // Dừng combo time
        $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', false)
            ->first();

        if ($activeComboTime) {
            $elapsedMinutes = $activeComboTime->isRunning()
                ? $activeComboTime->start_time->diffInMinutes(now())
                : 0;

            $activeComboTime->update([
                'end_time' => now(),
                'is_expired' => true,
                'remaining_minutes' => max(0, $activeComboTime->remaining_minutes - $elapsedMinutes)
            ]);
        }
    }

    private function getTableHourlyRate(Table $table): float
    {
        // Lấy hourly rate từ table_rates nếu có liên kết
        if ($table->table_rate_id) {
            $tableRate = TableRate::find($table->table_rate_id);
            if ($tableRate) {
                return $tableRate->hourly_rate;
            }
        }

        // Giá mặc định nếu không có table_rate
        return 50000.00;
    }

    private function calculateTableTimeInfo(Table $table): array
    {
        $timeInfo = [
            'is_running' => false,
            'is_paused' => false,
            'mode' => 'none',
            'hourly_rate' => $this->getTableHourlyRate($table),
            'elapsed_minutes' => 0,
            'current_cost' => 0,
            'total_minutes' => 0,
            'remaining_minutes' => 0,
            'is_near_end' => false,
            'paused_duration' => 0
        ];

        if (!$table->currentBill || $table->currentBill->status === 'quick') {
            return $timeInfo;
        }

        $bill = $table->currentBill;

        // Check combo time first
        $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', false)
            ->first();

        if ($activeComboTime) {
            $timeInfo['mode'] = 'combo';
            $timeInfo['total_minutes'] = $activeComboTime->total_minutes;

            if ($activeComboTime->isRunning()) {
                $timeInfo['is_running'] = true;
                $elapsedMinutes = $activeComboTime->start_time->diffInMinutes(now());
            } else {
                $timeInfo['is_paused'] = true;
                $elapsedMinutes = $activeComboTime->start_time->diffInMinutes($activeComboTime->end_time);
            }

            $timeInfo['elapsed_minutes'] = $elapsedMinutes;
            $timeInfo['remaining_minutes'] = max(0, $activeComboTime->remaining_minutes - $elapsedMinutes);
            $timeInfo['is_near_end'] = $timeInfo['remaining_minutes'] <= 30;

            // Tính phí phát sinh nếu vượt quá combo time
            if ($elapsedMinutes > $activeComboTime->total_minutes) {
                $extraMinutes = $elapsedMinutes - $activeComboTime->total_minutes;
                $timeInfo['current_cost'] = ($timeInfo['hourly_rate'] / 60) * $extraMinutes;
            }
        } else {
            // Regular time
            $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
                ->whereNull('end_time')
                ->first();

            if ($activeRegularTime) {
                $timeInfo['mode'] = 'regular';
                $timeInfo['is_running'] = $activeRegularTime->isRunning();
                $timeInfo['is_paused'] = $activeRegularTime->isPaused();
                $timeInfo['elapsed_minutes'] = $activeRegularTime->getElapsedMinutes();
                $timeInfo['paused_duration'] = $activeRegularTime->paused_duration ?? 0;

                $effectiveMinutes = $timeInfo['elapsed_minutes'] - $timeInfo['paused_duration'];
                $timeInfo['current_cost'] = ($timeInfo['hourly_rate'] / 60) * max(0, $effectiveMinutes);
            }
        }

        return $timeInfo;
    }
}
