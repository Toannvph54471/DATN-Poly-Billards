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
use App\Services\BillService;
use App\Http\Requests\Admin\Bill\StoreBillRequest;
use App\Http\Requests\Admin\Bill\AddProductRequest;
use App\Http\Requests\Admin\Bill\AddComboRequest;
use App\Enums\BillStatus;
use App\Enums\PaymentStatus;

class BillController extends Controller
{
   
    protected $billService;

    public function __construct(BillService $billService)
    {
        $this->billService = $billService;
    }

   public function index()
    {
        $bills = Bill::with([
            'table',
            'staff',
            'billTimeUsages',
            'billDetails.product'
        ])
            ->latest()
            ->paginate(10);

        return view('admin.bills.index', compact('bills'));
    }

    // ✅ Hàm hiển thị chi tiết hóa đơn
   public function show($id)
    {
        $bill = Bill::with([
            'table.tableRate',
            'user',
            'staff',
            'billTimeUsages',
            'billDetails.product.category',
            'billDetails.combo.comboItems.product',
            'payments'
        ])
            ->findOrFail($id);

        return view('admin.bills.show', compact('bill'));
    }

    /**
     * Tạo bill tính giờ thường
     */
    public function createBill(StoreBillRequest $request)
    {
        try {
            $bill = $this->billService->createBill($request->validated(), 'Open');

            return redirect()->route('admin.tables.detail', $request->table_id)
                ->with('success', 'Tạo hóa đơn tính giờ thành công');
        } catch (Exception $e) {
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
            $bill = $this->billService->createBill($request->all(), 'quick');

            return redirect()->route('admin.tables.detail', $request->table_id)
                ->with('success', 'Tạo hóa đơn bàn lẻ thành công');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi tạo hóa đơn: ' . $e->getMessage());
        }
    }

    /**
     * Thêm sản phẩm vào bill
     */
    public function addProductToBill(AddProductRequest $request, $billId)
    {
        try {
            $bill = Bill::findOrFail($billId);
            $this->billService->addProduct($bill, $request->product_id, $request->quantity);

            return redirect()->back()->with('success', 'Thêm sản phẩm thành công');
        } catch (Exception $e) {
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
            if (!in_array($bill->status, [BillStatus::Open, BillStatus::Quick])) {
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
            $this->billService->calculateBillTotal($bill);

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
    public function addComboToBill(AddComboRequest $request, $billId)
    {
        try {
            $bill = Bill::findOrFail($billId);
            $this->billService->addCombo($bill, $request->combo_id, $request->quantity);

            return redirect()->back()->with('success', 'Thêm combo thành công');
        } catch (Exception $e) {
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
            if ($bill->status !== BillStatus::Open) {
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
            // Note: getTableHourlyRate is now private in Service, but we need it here or move this logic to Service too.
            // For now, let's keep it simple and use hardcoded or fetch again, OR better: move this method to Service later.
            // But to fix the immediate error, I will fetch rate manually here or use a helper.
            $hourlyRate = 50000; // Fallback
            if ($bill->table->table_rate_id) {
                 $rate = TableRate::find($bill->table->table_rate_id);
                 if($rate) $hourlyRate = $rate->hourly_rate;
            }

            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $hourlyRate
            ]);

            // Cập nhật lại tổng tiền bill
            $this->billService->calculateBillTotal($bill);
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
            if ($bill->status !== \App\Enums\BillStatus::Quick) {
                return redirect()->back()->with('error', 'Chỉ có thể bắt đầu tính giờ từ bàn lẻ');
            }

            // Chuyển từ quick sang Open
            $bill->update([
                'status' => \App\Enums\BillStatus::Open
            ]);

            // Bắt đầu tính giờ thường
            $hourlyRate = 50000;
             if ($bill->table->table_rate_id) {
                 $rate = TableRate::find($bill->table->table_rate_id);
                 if($rate) $hourlyRate = $rate->hourly_rate;
            }
            
            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $hourlyRate
            ]);

            $this->billService->calculateBillTotal($bill);

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
            $this->billService->calculateBillTotal($bill);
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
            // Re-implement calculateElapsedMinutes locally or use Carbon
            $start = Carbon::parse($timeUsage->start_time);
            $end = now();
            $elapsedMinutes = $start->diffInMinutes($end);
            
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
     * Process payment for a bill
     */
    public function processPayment(Request $request, $billId)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,bank,card',
            'amount' => 'required|numeric|min:0',
            'cash_received' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        $bill = Bill::with(['table', 'user'])->findOrFail($billId);

        // Validate bill status
        if ($bill->status === BillStatus::Completed) {
            return redirect()
                ->route('admin.tables.detail', $bill->table_id)
                ->with('error', 'Hóa đơn này đã được thanh toán!');
        }

        DB::beginTransaction();
        try {
            // Stop all time usage
            $this->stopAllTimeUsage($bill);

            // Calculate final amount
            $this->billService->calculateBillTotal($bill);
            $bill->refresh();

            // Round to nearest 1000
            $finalAmount = ceil($bill->final_amount / 1000) * 1000;

            // Update bill status
            $bill->update([
                'status' => BillStatus::Completed,
                'payment_status' => PaymentStatus::Paid,
                'payment_method' => $request->payment_method,
                'final_amount' => $finalAmount,
                'note' => $request->note,
                'completed_at' => now(),
            ]);

            // Update table status
            $bill->table->update([
                'status' => 'available',
                'current_bill_id' => null,
            ]);

            // Update customer stats if exists
            if ($bill->user) {
                $bill->user->increment('total_visits');
                $bill->user->increment('total_spent', $finalAmount);
                $bill->user->update(['last_visit_at' => now()]);
            }

            DB::commit();

            return redirect()
                ->route('admin.tables.index')
                ->with('success', 'Thanh toán thành công! Tổng tiền: ' . number_format($finalAmount) . ' ₫');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment processing failed: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra khi thanh toán: ' . $e->getMessage());
        }
    }


    /**
     * Kích hoạt combo time
     */


    /**
     * Tính tổng tiền bill
     */


    /**
     * Tính tiền giờ chơi
     */


    /**
     * Lấy giá giờ của bàn
     */


    /**
     * Tính số phút đã trôi qua
     */


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
    /**
     * Hiển thị trang thanh toán
     */
    public function showPayment($id)
    {
        $bill = Bill::with([
            'table',
            'user',
            'billDetails.product',
            'billDetails.combo',
            'billTimeUsages',
            'payments'
        ])->findOrFail($id);

        // Tính toán thông tin giờ chơi
        $timeDetails = $this->calculateTimeChargeDetailed($bill);
        $timeCost = $timeDetails['totalCost'];

        // Tính tiền sản phẩm
        $productTotal = BillDetail::where('bill_id', $bill->id)
            ->where('is_combo_component', false)
            ->sum('total_price');

        $totalAmount = $timeCost + $productTotal;
        $finalAmount = $totalAmount - $bill->discount_amount;

        return view('admin.payments.payment', compact(
            'bill',
            'timeDetails',
            'timeCost',
            'productTotal',
            'totalAmount',
            'finalAmount'
        ));
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
        return 50000.00;
    }

    /**
     * Tính số phút đã trôi qua
     */
    private function calculateElapsedMinutes($timeUsage)
    {
        if ($timeUsage->paused_at) {
            return Carbon::parse($timeUsage->start_time)
                ->diffInMinutes(Carbon::createFromTimestamp($timeUsage->paused_at));
        } else {
            return Carbon::parse($timeUsage->start_time)->diffInMinutes(now());
        }
    }
}