<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Table;
use App\Models\Customer;
use App\Models\Combo;
use App\Models\Product;
use App\Models\ComboTimeUsage;
use App\Models\BillTimeUsage;
use App\Models\BillDetail;
use App\Models\Payment;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillController extends Controller
{
    public function createBill(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'user_phone' => 'nullable|string',
            'user_name' => 'nullable|string',
            'guest_count' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $table = Table::find($request->table_id);

            if ($table->status !== 'available') {
                return redirect()->back()->with('error', 'Bàn đang được sử dụng');
            }

            // Tìm hoặc tạo user với role mặc định
            $user = null;
            if ($request->user_phone) {
                $user = User::firstOrCreate(
                    ['phone' => $request->user_phone],
                    [
                        'name' => $request->user_name ?? 'Khách vãng lai',
                        'email' => $request->user_phone . '@customer.com',
                        'password' => bcrypt('customer123'),
                        'role_id' => 4,
                        'status' => 'active'
                    ]
                );
            }

            // Tạo bill number
            $billNumber = 'BILL' . date('Ymd') . str_pad(Bill::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Tạo bill với status là 'open' (tính giờ)
            $bill = Bill::create([
                'bill_number' => $billNumber,
                'table_id' => $request->table_id,
                'user_id' => $user?->id,
                'staff_id' => Auth::id(),
                'start_time' => now(),
                'status' => 'open',
                'payment_status' => 'pending',
                'total_amount' => 0,
                'discount_amount' => 0,
                'final_amount' => 0
            ]);

            // Khởi tạo bill_time_usage để bắt đầu tính giờ
            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $table->category->hourly_rate
            ]);

            // Cập nhật trạng thái bàn
            $table->update(['status' => 'occupied']);

            DB::commit();

            return redirect()->route('admin.tables.detail', $request->table_id)
                ->with('success', 'Tạo hóa đơn tính giờ thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi tạo hóa đơn: ' . $e->getMessage());
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

            $combo = Combo::with('items.product')->findOrFail($request->combo_id);

            // Thêm combo vào bill details
            $comboDetail = BillDetail::create([
                'bill_id' => $bill->id,
                'combo_id' => $combo->id,
                'quantity' => $request->quantity,
                'unit_price' => $combo->price,
                'original_price' => $combo->actual_value,
                'total_price' => $combo->price * $request->quantity
            ]);

            // Xử lý combo có giờ chơi
            if ($combo->is_time_combo && $combo->play_duration_minutes) {
                $this->activateComboTime($bill, $combo, $comboDetail);
            }

            // Xử lý các sản phẩm trong combo
            foreach ($combo->items as $item) {
                if ($item->product_id) {
                    // Kiểm tra tồn kho
                    if ($item->product->stock_quantity < $item->quantity * $request->quantity) {
                        throw new \Exception("Sản phẩm {$item->product->name} không đủ tồn kho");
                    }

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

            // Cập nhật tổng tiền
            $this->calculateBillTotal($bill);

            DB::commit();

            return redirect()->back()->with('success', 'Thêm combo thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi thêm combo: ' . $e->getMessage());
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
                return redirect()->back()->with('error', "Sản phẩm {$product->name} không đủ tồn kho");
            }

            // Thêm sản phẩm vào bill
            BillDetail::create([
                'bill_id' => $bill->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'unit_price' => $product->price,
                'original_price' => $product->price,
                'total_price' => $product->price * $request->quantity
            ]);

            // Cập nhật tồn kho
            $product->decrement('stock_quantity', $request->quantity);

            // Cập nhật tổng tiền
            $this->calculateBillTotal($bill);

            DB::commit();

            return redirect()->back()->with('success', 'Thêm sản phẩm thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi thêm sản phẩm: ' . $e->getMessage());
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

            // 🆕 THÊM: Bắt đầu tính giờ thường
            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $bill->table->category->hourly_rate
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Đã chuyển sang tính giờ thường');
        } catch (\Exception $e) {
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
            $tableRate = $bill->table->category->hourly_rate;
            $extraCharge = ($tableRate / 60) * $request->extra_minutes;

            // Chỉ cập nhật thời gian, KHÔNG dùng extra_charge
            $comboTimeUsage->update([
                'remaining_minutes' => $comboTimeUsage->remaining_minutes + $request->extra_minutes,
                'extra_minutes_added' => $comboTimeUsage->extra_minutes_added + $request->extra_minutes
                // KHÔNG cập nhật extra_charge
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
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi gia hạn thời gian: ' . $e->getMessage());
        }
    }

    public function activateComboTime(Bill $bill, Combo $combo, BillDetail $comboDetail)
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

        // 🆕 THÊM: Tạm dừng tính giờ thường (nếu có)
        $activeTimeUsage = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeTimeUsage) {
            $activeTimeUsage->update([
                'end_time' => now(),
                'duration_minutes' => $activeTimeUsage->start_time->diffInMinutes(now()),
                'total_price' => ($activeTimeUsage->hourly_rate / 60) * $activeTimeUsage->start_time->diffInMinutes(now())
            ]);
        }
    }

    public function pauseRegularTime(Bill $bill)
    {
        $activeTimeUsage = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeTimeUsage) {
            $activeTimeUsage->update([
                'end_time' => now(),
                'duration_minutes' => $activeTimeUsage->start_time->diffInMinutes(now()),
                'total_price' => ($activeTimeUsage->hourly_rate / 60) * $activeTimeUsage->start_time->diffInMinutes(now())
            ]);
        }
    }

    public function startRegularTime(Bill $bill)
    {
        $hourlyRate = $bill->table->category->hourly_rate;

        BillTimeUsage::create([
            'bill_id' => $bill->id,
            'start_time' => now(),
            'hourly_rate' => $hourlyRate
        ]);
    }


    public function stopAllTimeUsage(Bill $bill)
    {
        // Dừng regular time
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeRegularTime) {
            $endTime = now();
            $startTimestamp = strtotime($activeRegularTime->start_time);
            $elapsedMinutes = 0;

            if ($activeRegularTime->paused_at) {
                // Đang tạm dừng - tính đến thời điểm tạm dừng
                $pausedTimestamp = $activeRegularTime->paused_at;
                $elapsedMinutes = ($pausedTimestamp - $startTimestamp) / 60;
            } else {
                // Đang chạy - tính đến hiện tại
                $elapsedMinutes = (now()->timestamp - $startTimestamp) / 60;
            }

            // Trừ đi thời gian đã tạm dừng
            $effectiveMinutes = $elapsedMinutes - ($activeRegularTime->paused_duration ?? 0);

            $activeRegularTime->update([
                'end_time' => $endTime,
                'duration_minutes' => $elapsedMinutes,
                'total_price' => ($activeRegularTime->hourly_rate / 60) * max(0, $effectiveMinutes)
            ]);
        }

        // Dừng combo time
        $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', false)
            ->first();

        if ($activeComboTime) {
            $startTimestamp = strtotime($activeComboTime->start_time);
            $elapsedMinutes = (now()->timestamp - $startTimestamp) / 60;

            $activeComboTime->update([
                'end_time' => now(),
                'is_expired' => true,
                'remaining_minutes' => max(0, $activeComboTime->remaining_minutes - $elapsedMinutes)
            ]);
        }
    }

    public function calculateBillTotal(Bill $bill)
    {
        // Tính tiền sản phẩm (không bao gồm thành phần combo)
        $productTotal = BillDetail::where('bill_id', $bill->id)
            ->whereNull('combo_id')
            ->where('is_combo_component', false)
            ->sum('total_price');

        // Tính tiền combo
        $comboTotal = BillDetail::where('bill_id', $bill->id)
            ->whereNotNull('combo_id')
            ->sum('total_price');

        // Tính tiền giờ - SỬA LẠI: chỉ tính khi status là 'open'
        $timeTotal = 0;
        if ($bill->status === 'open') {
            $timeTotal = $this->calculateTimeCharge($bill);
        }

        $totalAmount = $productTotal + $comboTotal + $timeTotal;
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
        $hourlyRate = $bill->table->category->hourly_rate;
        $currentTimestamp = now()->timestamp;

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
            $startTimestamp = strtotime($activeRegularTime->start_time);
            $elapsedMinutes = 0;

            if ($activeRegularTime->paused_at) {
                // Đang tạm dừng - tính đến thời điểm tạm dừng (dùng timestamp)
                $pausedTimestamp = $activeRegularTime->paused_at;
                $elapsedMinutes = ($pausedTimestamp - $startTimestamp) / 60;
            } else {
                // Đang chạy - tính đến hiện tại
                $elapsedMinutes = ($currentTimestamp - $startTimestamp) / 60;
            }

            // Trừ đi thời gian đã tạm dừng
            $effectiveMinutes = $elapsedMinutes - ($activeRegularTime->paused_duration ?? 0);
            $totalTimeCost += ($activeRegularTime->hourly_rate / 60) * max(0, $effectiveMinutes);
        }

        return $totalTimeCost;
    }

    public function updateBillTotal($billId)
    {
        try {
            $bill = Bill::findOrFail($billId);
            $totalAmount = $this->calculateBillTotal($bill);

            return response()->json([
                'success' => true,
                'total_amount' => $bill->total_amount,
                'final_amount' => $bill->final_amount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function showPayment($id)
    {
        $bill = Bill::with([
            'table.category',
            'user',
            'billDetails.product',
            'billDetails.combo',
            'billTimeUsages',
            'comboTimeUsages'
        ])->findOrFail($id);

        // Tính toán chi phí giờ chơi
        $timeCost = $this->calculateTimeCharge($bill);

        // Tính tổng tiền sản phẩm
        $productTotal = BillDetail::where('bill_id', $bill->id)
            ->where(function ($query) {
                $query->whereNotNull('product_id')
                    ->orWhereNotNull('combo_id');
            })
            ->sum('total_price');

        // Thông tin thời gian
        $timeDetails = [
            'total_minutes' => 0,
            'hourly_rate' => $bill->table->category->hourly_rate
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

            // Tạo bản ghi thanh toán
            $payment = Payment::create([
                'bill_id' => $bill->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'transaction_id' => 'Cash',
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            // Cập nhật trạng thái bill
            $bill->update([
                'payment_status' => 'Paid',
                'end_time' => now(),
                'status' => 'Closed',
                'payment_method' => $request->payment_method
            ]);

            // Giải phóng bàn
            $bill->table->update(['status' => 'available']);

            // Cập nhật thông tin user (nếu có)
            if ($bill->user) {
                // Có thể thêm logic tracking cho user ở đây
                Log::info("User {$bill->user->name} đã thanh toán hóa đơn {$bill->bill_number}");
            }

            DB::commit();

            return redirect()->route('admin.tables.index')
                ->with('success', 'Thanh toán thành công. Hóa đơn: ' . $bill->bill_number);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi thanh toán: ' . $e->getMessage());
        }
    }


    public function createQuickBill(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'user_phone' => 'nullable|string',
            'user_name' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $table = Table::find($request->table_id);

            // Kiểm tra bàn đã có hóa đơn chưa
            if ($table->currentBill) {
                return redirect()->back()->with('error', 'Bàn đang có hóa đơn hoạt động');
            }

            // Tìm hoặc tạo user với role mặc định
            $user = null;
            if ($request->user_phone) {
                $user = User::firstOrCreate(
                    ['phone' => $request->user_phone],
                    [
                        'name' => $request->user_name ?? 'Khách vãng lai',
                        'email' => $request->user_phone . '@customer.com',
                        'password' => bcrypt('customer123'),
                        'role_id' => 4,
                        'status' => 'active'
                    ]
                );
            }

            // Tạo bill number
            $billNumber = 'QUICK' . date('Ymd') . str_pad(Bill::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Tạo bill với status là 'quick' (bàn lẻ)
            $bill = Bill::create([
                'bill_number' => $billNumber,
                'table_id' => $request->table_id,
                'user_id' => $user?->id,
                'staff_id' => Auth::id(),
                'start_time' => now(),
                'status' => 'quick', // Sửa thành 'quick' thay vì 'Quick'
                'payment_status' => 'pending',
                'total_amount' => 0,
                'discount_amount' => 0,
                'final_amount' => 0
            ]);

            // Cập nhật trạng thái bàn - SỬA LẠI THÀNH 'occupied'
            $table->update(['status' => 'occupied']);

            DB::commit();

            return redirect()->route('admin.tables.detail', $request->table_id)
                ->with('success', 'Tạo hóa đơn bàn lẻ thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi tạo hóa đơn: ' . $e->getMessage());
        }
    }


    public function convertToQuick($billId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);

            // Kiểm tra nếu đã là bàn lẻ
            if ($bill->status === 'quick') {
                return redirect()->back()->with('error', 'Hóa đơn đã là bàn lẻ');
            }

            // Dừng tất cả tính giờ
            $this->stopAllTimeUsage($bill);

            // Tính toán lại tổng tiền (quan trọng!)
            $this->calculateBillTotal($bill);

            // Chuyển thành bàn lẻ
            $bill->update([
                'status' => 'Quick',
                'end_time' => now()
            ]);

            // Trạng thái bàn vẫn là 'occupied'
            $bill->table->update(['status' => 'occupied']);

            DB::commit();

            return redirect()->back()->with('success', 'Đã chuyển thành bàn lẻ. Tổng tiền đã được cập nhật.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
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
                $startTimestamp = strtotime($activeRegularTime->start_time);
                $elapsedMinutes = (now()->timestamp - $startTimestamp) / 60;

                $activeRegularTime->update([
                    'paused_at' => now()->timestamp,
                    'duration_minutes' => $elapsedMinutes,
                    'paused_duration' => $activeRegularTime->paused_duration ?? 0
                ]);
            }

            // Dừng combo time nếu đang chạy
            $activeComboTime = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', false)
                ->whereNull('end_time')
                ->first();

            if ($activeComboTime) {
                $startTimestamp = strtotime($activeComboTime->start_time);
                $elapsedMinutes = (now()->timestamp - $startTimestamp) / 60;
                $remainingMinutes = max(0, $activeComboTime->remaining_minutes - $elapsedMinutes);

                $activeComboTime->update([
                    'end_time' => now(),
                    'remaining_minutes' => $remainingMinutes
                ]);
            }

            $this->calculateBillTotal($bill);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Đã tạm dừng tính giờ']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi khi tạm dừng: ' . $e->getMessage()]);
        }
    }

    public function resumeTime($billId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);
            $table = $bill->table;

            // Resume regular time
            $pausedRegularTime = BillTimeUsage::where('bill_id', $billId)
                ->whereNotNull('paused_at')
                ->whereNull('end_time')
                ->first();

            if ($pausedRegularTime) {
                // Tính thời gian đã pause (từ paused_at đến now)
                $pausedDurationMinutes = (now()->timestamp - $pausedRegularTime->paused_at) / 60;

                $pausedRegularTime->update([
                    'paused_duration' => ($pausedRegularTime->paused_duration ?? 0) + $pausedDurationMinutes,
                    'paused_at' => null, // Reset về null
                    'start_time' => now() // Reset start_time về thời điểm resume
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
                    BillTimeUsage::create([
                        'bill_id' => $bill->id,
                        'start_time' => now(),
                        'hourly_rate' => $table->category->hourly_rate
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Đã tiếp tục tính giờ']);
        } catch (\Exception $e) {
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

            // Chuyển từ quick sang open
            $bill->update([
                'status' => 'open',
                'start_time' => now()
            ]);

            // Bắt đầu tính giờ thường
            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $bill->table->category->hourly_rate
            ]);

            $this->calculateBillTotal($bill);

            DB::commit();

            return redirect()->back()->with('success', 'Đã bắt đầu tính giờ chơi');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function getTableTimeInfo($tableId)
    {
        try {
            $table = Table::with([
                'currentBill.billTimeUsages',
                'currentBill.comboTimeUsages',
                'category'
            ])->findOrFail($tableId);

            $timeInfo = [
                'is_running' => false,
                'is_paused' => false,
                'mode' => 'none',
                'hourly_rate' => $table->category->hourly_rate ?? 0,
                'elapsed_minutes' => 0,
                'current_cost' => 0,
                'total_minutes' => 0,
                'is_near_end' => false,
                'paused_duration' => 0
            ];

            if (!$table->currentBill || $table->currentBill->status === 'quick') {
                return $timeInfo;
            }

            // Lấy thông tin giờ thường
            $activeRegularTime = BillTimeUsage::where('bill_id', $table->currentBill->id)
                ->whereNull('end_time')
                ->first();

            // Lấy thông tin combo time
            $activeComboTime = ComboTimeUsage::where('bill_id', $table->currentBill->id)
                ->where('is_expired', false)
                ->first();

            // Xác định mode
            if ($activeComboTime) {
                $timeInfo['mode'] = 'combo';
                $timeInfo['total_minutes'] = $activeComboTime->total_minutes;

                // Tính thời gian đã sử dụng combo
                if ($activeComboTime->end_time) {
                    // Đang tạm dừng
                    $elapsedMinutes = $activeComboTime->start_time->diffInMinutes($activeComboTime->end_time);
                    $timeInfo['is_paused'] = true;
                } else {
                    // Đang chạy
                    $elapsedMinutes = $activeComboTime->start_time->diffInMinutes(now());
                    $timeInfo['is_running'] = true;
                }

                $timeInfo['elapsed_minutes'] = $elapsedMinutes;
                $timeInfo['remaining_minutes'] = max(0, $activeComboTime->remaining_minutes - $elapsedMinutes);
                $timeInfo['is_near_end'] = $timeInfo['remaining_minutes'] <= 30;

                // Tính chi phí phát sinh nếu vượt quá combo time
                if ($elapsedMinutes > $activeComboTime->total_minutes) {
                    $extraMinutes = $elapsedMinutes - $activeComboTime->total_minutes;
                    $timeInfo['current_cost'] = ($timeInfo['hourly_rate'] / 60) * $extraMinutes;
                }
            } elseif ($activeRegularTime) {
                $timeInfo['mode'] = 'regular';

                if ($activeRegularTime->paused_at) {
                    // Đang tạm dừng
                    $timeInfo['is_paused'] = true;
                    $elapsedMinutes = $activeRegularTime->start_time->diffInMinutes($activeRegularTime->paused_at);
                } else {
                    // Đang chạy
                    $timeInfo['is_running'] = true;
                    $elapsedMinutes = $activeRegularTime->start_time->diffInMinutes(now());
                }

                $timeInfo['elapsed_minutes'] = $elapsedMinutes;
                $timeInfo['paused_duration'] = $activeRegularTime->paused_duration ?? 0;

                // Tính chi phí hiện tại (trừ thời gian tạm dừng)
                $effectiveMinutes = $elapsedMinutes - $timeInfo['paused_duration'];
                $timeInfo['current_cost'] = ($timeInfo['hourly_rate'] / 60) * max(0, $effectiveMinutes);
            }

            return $timeInfo;
        } catch (\Exception $e) {
            Log::error("Error getting table time info: " . $e->getMessage());
            return [
                'is_running' => false,
                'is_paused' => false,
                'mode' => 'none',
                'hourly_rate' => 0,
                'elapsed_minutes' => 0,
                'current_cost' => 0,
                'total_minutes' => 0,
                'is_near_end' => false,
                'paused_duration' => 0
            ];
        }
    }
}
