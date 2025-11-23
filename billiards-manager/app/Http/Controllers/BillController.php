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
    public function index()
    {
        $bill = Bill::latest()->paginate(10);
        return view('admin.bills.index', compact('bill'));
    }

    // ✅ Hàm hiển thị chi tiết hóa đơn
    public function show($id)
    {
        $bill = Bill::findOrFail($id);
        return view('admin.bills.show', compact('bill'));
    }

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

                // CẬP NHẬT SỐ LẦN GHÉ QUA - THÊM ĐOẠN NÀY
                $user->increment('total_visits');

                // Hoặc nếu bạn muốn cập nhật customer_type dựa trên số lần ghé qua
                $this->updateCustomerType($user);
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

    // THÊM HÀM NÀY ĐỂ CẬP NHẬT LOẠI KHÁCH HÀNG
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

        $user->save();
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
     * Xóa sản phẩm khỏi bill (chỉ áp dụng cho sản phẩm thông thường, không phải thành phần combo)
     */
    public function removeProductFromBill(Request $request, $billId, $billDetailId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);
            $billDetail = BillDetail::where('id', $billDetailId)
                ->where('bill_id', $billId)
                ->firstOrFail();

            // Kiểm tra xem bill có đang ở trạng thái có thể xóa sản phẩm không
            if (!in_array($bill->status, ['Open', 'quick'])) {
                return redirect()->back()->with('error', 'Chỉ có thể xóa sản phẩm khỏi bill đang mở');
            }

            // KHÔNG cho phép xóa nếu là thành phần của combo
            if ($billDetail->is_combo_component) {
                return redirect()->back()->with('error', 'Không thể xóa sản phẩm là thành phần của combo');
            }

            // KHÔNG cho phép xóa nếu là combo
            if ($billDetail->combo_id) {
                return redirect()->back()->with('error', 'Không thể xóa combo bằng chức năng này. Vui lòng sử dụng chức năng xóa combo.');
            }

            // Chỉ xử lý với sản phẩm thông thường
            if ($billDetail->product_id) {
                // Hoàn trả tồn kho
                $product = Product::find($billDetail->product_id);
                if ($product) {
                    $product->increment('stock_quantity', $billDetail->quantity);
                    Log::info("Restored stock for product: {$product->name}, quantity: {$billDetail->quantity}");
                }
            }

            // Xóa bản ghi bill detail
            $billDetail->delete();

            // Cập nhật lại tổng tiền bill
            $this->calculateBillTotal($bill);

            DB::commit();

            return redirect()->back()->with('success', 'Xóa sản phẩm khỏi bill thành công');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Remove product from bill error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi xóa sản phẩm: ' . $e->getMessage());
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
     * DỪNG COMBO TIME - KHI HẾT THỜI GIAN HOẶC NHÂN VIÊN DỪNG THỦ CÔNG
     */
    public function stopComboTime($billId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::findOrFail($billId);

            // Tìm combo time đang chạy hoặc đang tạm dừng
            $activeComboTime = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', false)
                ->first();

            if (!$activeComboTime) {
                return redirect()->back()->with('error', 'Không tìm thấy combo thời gian đang chạy');
            }

            // Tính thời gian đã sử dụng nếu đang chạy
            if (is_null($activeComboTime->end_time)) {
                $start = Carbon::parse($activeComboTime->start_time);
                $elapsedMinutes = $start->diffInMinutes(now());
                $remainingMinutes = max(0, $activeComboTime->remaining_minutes - $elapsedMinutes);
            } else {
                // Đang tạm dừng, sử dụng remaining_minutes hiện tại
                $remainingMinutes = $activeComboTime->remaining_minutes;
            }

            // Dừng combo time
            $activeComboTime->update([
                'end_time' => now(),
                'remaining_minutes' => $remainingMinutes,
                'is_expired' => true
            ]);

            // Cập nhật trạng thái bill nếu cần
            $bill->refresh();

            DB::commit();

            return redirect()->back()->with('success', 'Đã dừng combo thời gian. Bạn có thể bật giờ thường nếu cần.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error stopping combo time: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi dừng combo: ' . $e->getMessage());
        }
    }

    /**
     * Chuyển từ combo time sang tính giờ thường
     */
    public function switchToRegularTime(Request $request, $billId)
    {
        try {
            DB::beginTransaction();

            $bill = Bill::with('table')->findOrFail($billId);

            // Kiểm tra trạng thái bill
            if ($bill->status !== 'Open') {
                return redirect()->back()->with('error', 'Chỉ có thể chuyển sang giờ thường cho bill đang mở');
            }

            // Kiểm tra xem có combo time đã hết hạn không
            $expiredComboTime = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', true)
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

            // Cập nhật lại tổng tiền bill
            $this->calculateBillTotal($bill);
            $bill->refresh();

            DB::commit();

            return redirect()->route('admin.tables.detail', $bill->table_id)
                ->with('success', 'Đã chuyển sang tính giờ thường thành công');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error switching to regular time: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi chuyển sang giờ thường: ' . $e->getMessage());
        }
    }



    /**
     * Kiểm tra và cập nhật trạng thái combo time real-time
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

            // Kiểm tra combo đã dừng (hết thời gian hoặc dừng thủ công)
            $stoppedComboTime = ComboTimeUsage::where('bill_id', $billId)
                ->where('is_expired', true)
                ->first();

            // TỰ ĐỘNG DỪNG COMBO KHI HẾT THỜI GIAN
            if ($activeComboTime) {
                $start = Carbon::parse($activeComboTime->start_time);
                $elapsedMinutes = $start->diffInMinutes(now());
                $remainingMinutes = max(0, $activeComboTime->remaining_minutes - $elapsedMinutes);

                // Nếu hết thời gian, tự động dừng combo
                if ($remainingMinutes <= 0) {
                    $activeComboTime->update([
                        'end_time' => now(),
                        'remaining_minutes' => 0,
                        'is_expired' => true
                    ]);

                    return [
                        'has_active_combo' => false,
                        'has_expired_combo' => true,
                        'is_near_end' => false,
                        'is_expired' => true,
                        'needs_switch' => true, // Cần chuyển sang giờ thường
                        'remaining_minutes' => 0,
                        'mode' => 'combo_ended'
                    ];
                }

                $isNearEnd = $remainingMinutes <= 10 && $remainingMinutes > 0;

                return [
                    'has_active_combo' => true,
                    'has_expired_combo' => false,
                    'is_near_end' => $isNearEnd,
                    'is_expired' => false,
                    'needs_switch' => false,
                    'remaining_minutes' => $remainingMinutes,
                    'elapsed_minutes' => $elapsedMinutes,
                    'mode' => 'combo'
                ];
            }

            if (!$activeComboTime && $stoppedComboTime) {
                return [
                    'has_active_combo' => false,
                    'has_expired_combo' => true,
                    'is_near_end' => false,
                    'is_expired' => true,
                    'needs_switch' => true, // Cần chuyển sang giờ thường
                    'remaining_minutes' => 0,
                    'mode' => 'combo_ended'
                ];
            }

            return [
                'has_active_combo' => false,
                'has_expired_combo' => false,
                'is_near_end' => false,
                'is_expired' => false,
                'needs_switch' => false,
                'remaining_minutes' => 0
            ];
        } catch (\Exception $e) {
            Log::error('Error checking combo time: ' . $e->getMessage());
            return [
                'has_active_combo' => false,
                'has_expired_combo' => false,
                'is_near_end' => false,
                'is_expired' => false,
                'needs_switch' => false
            ];
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

                // LÀM TRÒN PHÚT: làm tròn lên đến phút
                $roundedMinutes = ceil($effectiveMinutes);
                $timeCost = ($timeUsage->hourly_rate / 60) * max(0, $roundedMinutes);

                $totalTimeCost += $timeCost;
            }
        }

        return $totalTimeCost;
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

                // 4. XỬ LÝ THỜI GIAN VÀ GIÁ CẢ TRƯỚC KHI CHUYỂN

                // Lấy giá giờ của bàn cũ và bàn mới
                $sourceHourlyRate = $this->getTableHourlyRate($sourceTable);
                $targetHourlyRate = $this->getTableHourlyRate($targetTable);

                // Xử lý giờ thường đang chạy
                $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
                    ->whereNull('end_time')
                    ->first();

                if ($activeRegularTime) {
                    // Tính thời gian đã sử dụng ở bàn cũ
                    $startTime = Carbon::parse($activeRegularTime->start_time);
                    $elapsedMinutes = $startTime->diffInMinutes(now());
                    $effectiveMinutes = $elapsedMinutes - ($activeRegularTime->paused_duration ?? 0);

                    // Tính tiền giờ đã sử dụng ở bàn cũ
                    $timeCost = ($sourceHourlyRate / 60) * max(0, $effectiveMinutes);

                    // Kết thúc session giờ thường ở bàn cũ
                    $activeRegularTime->update([
                        'end_time' => now(),
                        'duration_minutes' => $elapsedMinutes,
                        'total_price' => $timeCost
                    ]);

                    // Tạo session giờ thường mới ở bàn mới
                    BillTimeUsage::create([
                        'bill_id' => $bill->id,
                        'start_time' => now(),
                        'hourly_rate' => $targetHourlyRate
                    ]);
                }

                // Xử lý combo time đang chạy
                $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
                    ->where('is_expired', false)
                    ->whereNull('end_time')
                    ->first();

                if ($activeComboTime) {
                    // Tính thời gian đã sử dụng của combo
                    $startTime = Carbon::parse($activeComboTime->start_time);
                    $elapsedMinutes = $startTime->diffInMinutes(now());
                    $remainingMinutes = max(0, $activeComboTime->remaining_minutes - $elapsedMinutes);

                    // Cập nhật thời gian còn lại và tạm dừng combo
                    $activeComboTime->update([
                        'end_time' => now(),
                        'remaining_minutes' => $remainingMinutes
                    ]);

                    // Chuyển combo sang bàn mới và tiếp tục
                    $activeComboTime->update([
                        'table_id' => $targetTable->id,
                        'start_time' => now(),
                        'end_time' => null
                    ]);
                }

                // 5. Cập nhật bill sang bàn mới
                $bill->update([
                    'table_id' => $targetTable->id,
                    'note' => $bill->note . " [Chuyển từ bàn {$sourceTable->table_number} lúc " . now()->format('H:i d/m/Y') . "]"
                ]);

                // 6. Cập nhật trạng thái bàn
                $sourceTable->update(['status' => 'available']);
                $targetTable->update(['status' => 'occupied']);

                // 7. Cập nhật tổng tiền bill
                $this->calculateBillTotal($bill);

                // 8. Log hoạt động
                Log::info('Chuyển bàn thành công', [
                    'bill_id' => $bill->id,
                    'bill_number' => $bill->bill_number,
                    'source_table' => $sourceTable->table_number,
                    'target_table' => $targetTable->table_number,
                    'source_hourly_rate' => $sourceHourlyRate,
                    'target_hourly_rate' => $targetHourlyRate,
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
     * Xử lý chuyển bàn với chi tiết thời gian
     */
    private function handleTableTransferTime(Bill $bill, Table $sourceTable, Table $targetTable)
    {
        $sourceHourlyRate = $this->getTableHourlyRate($sourceTable);
        $targetHourlyRate = $this->getTableHourlyRate($targetTable);

        $timeDetails = [
            'source_hourly_rate' => $sourceHourlyRate,
            'target_hourly_rate' => $targetHourlyRate,
            'transfer_time' => now(),
            'elapsed_minutes' => 0,
            'time_cost' => 0
        ];

        // Xử lý giờ thường đang chạy
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeRegularTime) {
            // Tính thời gian đã sử dụng ở bàn cũ
            $startTime = Carbon::parse($activeRegularTime->start_time);
            $elapsedMinutes = $startTime->diffInMinutes(now());
            $effectiveMinutes = $elapsedMinutes - ($activeRegularTime->paused_duration ?? 0);

            // Tính tiền giờ đã sử dụng ở bàn cũ
            $timeCost = ($sourceHourlyRate / 60) * max(0, $effectiveMinutes);

            // Kết thúc session giờ thường ở bàn cũ
            $activeRegularTime->update([
                'end_time' => now(),
                'duration_minutes' => $elapsedMinutes,
                'total_price' => $timeCost
            ]);

            // Tạo session giờ thường mới ở bàn mới
            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $targetHourlyRate,
                'note' => "Chuyển từ bàn {$sourceTable->table_number}"
            ]);

            $timeDetails['elapsed_minutes'] = $effectiveMinutes;
            $timeDetails['time_cost'] = $timeCost;
            $timeDetails['has_regular_time'] = true;
        }

        // Xử lý combo time đang chạy
        $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', false)
            ->whereNull('end_time')
            ->first();

        if ($activeComboTime) {
            // Tính thời gian đã sử dụng của combo
            $startTime = Carbon::parse($activeComboTime->start_time);
            $elapsedMinutes = $startTime->diffInMinutes(now());
            $remainingMinutes = max(0, $activeComboTime->remaining_minutes - $elapsedMinutes);

            // Cập nhật thời gian còn lại và tạm dừng combo
            $activeComboTime->update([
                'end_time' => now(),
                'remaining_minutes' => $remainingMinutes
            ]);

            // Chuyển combo sang bàn mới và tiếp tục
            $activeComboTime->update([
                'table_id' => $targetTable->id,
                'start_time' => now(),
                'end_time' => null
            ]);

            $timeDetails['has_combo_time'] = true;
            $timeDetails['combo_remaining_minutes'] = $remainingMinutes;
        }

        return $timeDetails;
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

            // Kiểm tra nếu có redirect từ processPayment
            if (session('redirect_after_print')) {
                $redirectUrl = session('redirect_after_print');
                session()->forget('redirect_after_print');

                return view('admin.bills.print', array_merge($billData, [
                    'autoRedirect' => true,
                    'redirectUrl' => $redirectUrl
                ]));
            }

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
            'hourlyRate' => 0,
            'tableTransfers' => [] // Thêm thông tin chuyển bàn
        ];

        // Lấy tất cả session giờ thường
        $allRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->orderBy('created_at')
            ->get();

        foreach ($allRegularTime as $timeUsage) {
            $sessionCost = $timeUsage->total_price ?? 0;
            $sessionMinutes = $timeUsage->duration_minutes ?? 0;

            // Nếu là session đang chạy, tính toán thời gian thực
            if (is_null($timeUsage->end_time)) {
                $elapsedMinutes = $this->calculateElapsedMinutes($timeUsage);
                $effectiveMinutes = $elapsedMinutes - ($timeUsage->paused_duration ?? 0);
                $sessionCost = ($timeUsage->hourly_rate / 60) * max(0, $effectiveMinutes);
                $sessionMinutes = $effectiveMinutes;
            }

            $timeDetails['sessions'][] = [
                'type' => is_null($timeUsage->end_time) ? 'regular_active' : 'regular_ended',
                'minutes' => $sessionMinutes,
                'hours' => round($sessionMinutes / 60, 2),
                'hourly_rate' => $timeUsage->hourly_rate,
                'cost' => $sessionCost,
                'description' => "Giờ thường: " . $this->formatDuration($sessionMinutes),
                'calculation' => $this->formatTimeCalculation($timeUsage->hourly_rate, $sessionMinutes, $sessionCost),
                'table_note' => $timeUsage->note // Hiển thị ghi chú chuyển bàn nếu có
            ];

            $timeDetails['totalCost'] += $sessionCost;
            $timeDetails['totalMinutes'] += $sessionMinutes;

            if ($timeUsage->hourly_rate > 0) {
                $timeDetails['hourlyRate'] = $timeUsage->hourly_rate;
            }
        }

        // Xử lý combo time (giữ nguyên như cũ)
        $activeComboTime = ComboTimeUsage::where('bill_id', $bill->id)
            ->where('is_expired', false)
            ->where('remaining_minutes', '>', 0)
            ->first();

        if (!$activeComboTime) {
            $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
                ->whereNull('end_time')
                ->get();

            foreach ($activeRegularTime as $timeUsage) {
                // Đã xử lý ở trên
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

    public function getCustomerStats($userId)
    {
        $user = User::find($userId);

        $stats = [
            'total_visits' => $user->total_visits,
            'total_spent' => $user->total_spent,
            'average_spent' => $user->total_visits > 0 ? $user->total_spent / $user->total_visits : 0,
            'customer_type' => $user->customer_type,
            'last_visit' => $user->last_visit_date,
        ];

        return $stats;
    }
}
