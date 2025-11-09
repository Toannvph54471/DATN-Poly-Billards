<?php
// app/Http/Controllers/BillController.php
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
            'customer_phone' => 'nullable|string',
            'customer_name' => 'nullable|string',
            'guest_count' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $table = Table::find($request->table_id);

            if ($table->status !== 'available') {
                return redirect()->back()->with('error', 'Bàn đang được sử dụng');
            }

            // Tìm hoặc tạo khách hàng
            $customer = null;
            if ($request->customer_phone) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $request->customer_phone],
                    [
                        'name' => $request->customer_name ?? 'Khách vãng lai',
                        'customer_type' => 'New'
                    ]
                );
            }

            // Tạo bill number
            $billNumber = 'BILL' . date('Ymd') . str_pad(Bill::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Tạo bill
            $bill = Bill::create([
                'bill_number' => $billNumber,
                'table_id' => $request->table_id,
                'customer_id' => $customer?->id,
                'staff_id' => Auth::id(),
                'start_time' => now(),
                'status' => 'Open',
                'payment_status' => 'Pending',
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

            // 🆕 THÊM: Tính toán tổng tiền ngay sau khi tạo bill
            $this->calculateBillTotal($bill);

            // Cập nhật trạng thái bàn
            $table->update(['status' => 'occupied']);

            DB::commit();

            return redirect()->route('admin.tables.detail', $request->table_id)
                ->with('success', 'Tạo hóa đơn thành công');
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

    // Trong BillController - sửa phương thức stopAllTimeUsage
    public function stopAllTimeUsage(Bill $bill)
    {
        // Dừng regular time
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeRegularTime) {
            $activeRegularTime->update([
                'end_time' => now(),
                'duration_minutes' => $activeRegularTime->start_time->diffInMinutes(now()),
                'total_price' => ($activeRegularTime->hourly_rate / 60) * $activeRegularTime->start_time->diffInMinutes(now())
            ]);
        }

        // Dừng combo time
        $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', false)
            ->first();

        if ($activeComboTime) {
            $elapsed = $activeComboTime->start_time->diffInMinutes(now());
            $activeComboTime->update([
                'end_time' => now(),
                'is_expired' => true,
                'remaining_minutes' => max(0, $activeComboTime->remaining_minutes - $elapsed)
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

        // Chỉ tính tiền giờ nếu bill đang Open (đang chơi)
        $timeTotal = 0;
        if ($bill->status === 'Open') {
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

        // 1. Tính tiền giờ thường đã kết thúc
        $endedRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNotNull('end_time')
            ->sum('total_price');

        // 2. Tính tiền giờ thường đang chạy
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeRegularTime) {
            $elapsedMinutes = $activeRegularTime->start_time->diffInMinutes(now());
            $activeCost = ($activeRegularTime->hourly_rate / 60) * $elapsedMinutes;
            $totalTimeCost += $activeCost;
        }

        $totalTimeCost += $endedRegularTime;

        // 3. Tính phí phát sinh từ combo time (gia hạn)
        $comboTimeUsages = ComboTimeUsage::where('bill_id', $bill->id)->get();
        foreach ($comboTimeUsages as $usage) {
            if ($usage->extra_minutes_added > 0) {
                $tableRate = $bill->table->category->hourly_rate;
                $totalTimeCost += ($tableRate / 60) * $usage->extra_minutes_added;
            }
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

    // app/Http\Controllers/BillController.php

    public function showPayment($id)
    {
        $bill = Bill::with([
            'table.category',
            'customer',
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

            // Tạo bản ghi thanh toán trong bảng payments
            $payment = Payment::create([
                'bill_id' => $bill->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'transaction_id' => 'Cash', // hoặc có thể là mã giao dịch từ cổng thanh toán
                'status' => 'completed', // hoặc 'pending', tùy theo luồng của bạn
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

            // Cập nhật thông tin khách hàng
            if ($bill->customer) {
                $bill->customer->increment('total_visits');
                $bill->customer->increment('total_spent', $bill->final_amount);
                $bill->customer->update(['last_visit_at' => now()]);
            }

            DB::commit();

            return redirect()->route('admin.tables.index')
                ->with('success', 'Thanh toán thành công. Hóa đơn: ' . $bill->bill_number);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi thanh toán: ' . $e->getMessage());
        }
    }

    // Thêm method createQuickBill trong BillController
    public function createQuickBill(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_phone' => 'nullable|string',
            'customer_name' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $table = Table::find($request->table_id);

            // Tìm hoặc tạo khách hàng
            $customer = null;
            if ($request->customer_phone) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $request->customer_phone],
                    [
                        'name' => $request->customer_name ?? 'Khách vãng lai',
                        'customer_type' => 'New'
                    ]
                );
            }

            // Tạo bill number
            $billNumber = 'QUICK' . date('Ymd') . str_pad(Bill::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Tạo bill với status là Quick (bàn lẻ)
            $bill = Bill::create([
                'bill_number' => $billNumber,
                'table_id' => $request->table_id,
                'customer_id' => $customer?->id,
                'staff_id' => Auth::id(),
                'start_time' => now(),
                'status' => 'Quick', // QUAN TRỌNG: Status mới cho bàn lẻ
                'payment_status' => 'Pending',
                'total_amount' => 0,
                'discount_amount' => 0,
                'final_amount' => 0
            ]);

            // KHÔNG tạo bill_time_usage vì không tính giờ

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

            // Dừng tất cả tính giờ
            $this->stopAllTimeUsage($bill);

            // Chuyển thành bàn lẻ
            $bill->update([
                'status' => 'Quick',
                'end_time' => now()
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Đã chuyển thành bàn lẻ');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function startPlaying($billId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);
            $table = $bill->table;

            // Chuyển từ bàn lẻ sang bàn chơi
            $bill->update([
                'status' => 'Open',
                'start_time' => now(),
                'end_time' => null
            ]);

            // Bắt đầu tính giờ
            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $table->category->hourly_rate
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Đã bắt đầu tính giờ chơi');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    
}
