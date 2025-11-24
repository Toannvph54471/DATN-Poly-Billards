<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\BillTimeUsage;
use App\Models\Combo;
use App\Models\ComboTimeUsage;
use App\Models\Product;
use App\Models\Table;
use App\Models\TableRate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class BillService
{
    /**
     * Create a new bill (Time-based or Quick)
     */
    public function createBill(array $data, string $type = 'Open')
    {
        return DB::transaction(function () use ($data, $type) {
            $table = Table::findOrFail($data['table_id']);

            if ($table->status !== 'available') {
                throw new Exception('Bàn đang được sử dụng');
            }

            // Find or create user
            $user = null;
            if (!empty($data['user_phone'])) {
                $user = User::firstOrCreate(
                    ['phone' => $data['user_phone']],
                    [
                        'name' => $data['user_name'] ?? 'Khách vãng lai',
                        'email' => $data['user_phone'] . '@customer.com',
                        'password' => bcrypt(Str::random(8)),
                        'role_id' => 4, // Customer role
                        'status' => 'Active'
                    ]
                );
            }

            // Generate Bill Number
            $prefix = $type === 'quick' ? 'QUICK' : 'BILL';
            $billNumber = $prefix . date('Ymd') . str_pad(Bill::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Create Bill
            $bill = Bill::create([
                'bill_number' => $billNumber,
                'table_id' => $table->id,
                'user_id' => $user?->id,
                'reservation_id' => $data['reservation_id'] ?? null,
                'staff_id' => Auth::id(),
                'start_time' => now(),
                'status' => $type === 'quick' ? \App\Enums\BillStatus::Quick : \App\Enums\BillStatus::Open,
                'payment_status' => \App\Enums\PaymentStatus::Pending,
                'total_amount' => 0,
                'discount_amount' => 0,
                'final_amount' => 0
            ]);

            // If Time-based, start timer
            if ($type === 'Open') {
                $hourlyRate = $this->getTableHourlyRate($table);
                BillTimeUsage::create([
                    'bill_id' => $bill->id,
                    'start_time' => now(),
                    'hourly_rate' => $hourlyRate
                ]);
            }

            // Update Table Status
            $table->update(['status' => 'occupied']);

            // Update Reservation if exists
            if (!empty($data['reservation_id'])) {
                $bill->reservation()->update([
                    'status' => 'CheckedIn',
                    'checked_in_at' => now()
                ]);
            }

            return $bill;
        });
    }

    /**
     * Add Product to Bill
     */
    public function addProduct(Bill $bill, int $productId, int $quantity)
    {
        return DB::transaction(function () use ($bill, $productId, $quantity) {
            $product = Product::findOrFail($productId);

            if ($product->stock_quantity < $quantity) {
                throw new Exception("Sản phẩm {$product->name} không đủ tồn kho. Còn: {$product->stock_quantity}");
            }

            BillDetail::create([
                'bill_id' => $bill->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'original_price' => $product->price,
                'total_price' => $product->price * $quantity,
                'is_combo_component' => false
            ]);

            $product->decrement('stock_quantity', $quantity);

            $this->calculateBillTotal($bill);

            return true;
        });
    }
    /**
     * Add Combo to Bill
     */
    public function addCombo(Bill $bill, int $comboId, int $quantity)
    {
        return DB::transaction(function () use ($bill, $comboId, $quantity) {
            if ($bill->status === \App\Enums\BillStatus::Quick) {
                throw new Exception('Bàn lẻ không thể thêm combo');
            }

            $combo = Combo::with('comboItems.product')->findOrFail($comboId);

            // Check stock
            foreach ($combo->comboItems as $item) {
                if ($item->product && $item->product->stock_quantity < ($item->quantity * $quantity)) {
                    throw new Exception("{$item->product->name} không đủ tồn kho.");
                }
            }

            // Add Combo Detail
            $comboDetail = BillDetail::create([
                'bill_id' => $bill->id,
                'combo_id' => $combo->id,
                'quantity' => $quantity,
                'unit_price' => $combo->price,
                'original_price' => $combo->actual_value,
                'total_price' => $combo->price * $quantity,
                'is_combo_component' => false
            ]);

            // Add Combo Items
            foreach ($combo->comboItems as $item) {
                if ($item->product_id) {
                    BillDetail::create([
                        'bill_id' => $bill->id,
                        'product_id' => $item->product_id,
                        'parent_bill_detail_id' => $comboDetail->id,
                        'quantity' => $item->quantity * $quantity,
                        'unit_price' => 0,
                        'original_price' => $item->product->price,
                        'total_price' => 0,
                        'is_combo_component' => true
                    ]);

                    $item->product->decrement('stock_quantity', $item->quantity * $quantity);
                }
            }

            // Activate Combo Time if applicable
            if ($combo->is_time_combo && $combo->play_duration_minutes) {
                $this->activateComboTime($bill, $combo);
            }

            $this->calculateBillTotal($bill);

            return true;
        });
    }

    /**
     * Calculate Bill Total
     */
    public function calculateBillTotal(Bill $bill)
    {
        $productTotal = BillDetail::where('bill_id', $bill->id)
            ->where('is_combo_component', false)
            ->sum('total_price');

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
     * Calculate Time Charge
     */
    public function calculateTimeCharge(Bill $bill)
    {
        $totalTimeCost = 0;

        // 1. Ended Regular Time
        $endedRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNotNull('end_time')
            ->get();

        foreach ($endedRegularTime as $timeUsage) {
            $totalTimeCost += $timeUsage->total_price ?? 0;
        }

        // 2. Active Regular Time (only if no active combo)
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
                $roundedMinutes = ceil($effectiveMinutes);
                $timeCost = ($timeUsage->hourly_rate / 60) * max(0, $roundedMinutes);
                $totalTimeCost += $timeCost;
            }
        }

        return $totalTimeCost;
    }

    /**
     * Activate Combo Time logic
     */
    private function activateComboTime(Bill $bill, Combo $combo)
    {
        // Stop current regular time
        $activeRegularTime = BillTimeUsage::where('bill_id', $bill->id)
            ->whereNull('end_time')
            ->first();

        if ($activeRegularTime) {
            $elapsedMinutes = $this->calculateElapsedMinutes($activeRegularTime);
            $effectiveMinutes = $elapsedMinutes - ($activeRegularTime->paused_duration ?? 0);
            $regularTimeCost = ($activeRegularTime->hourly_rate / 60) * max(0, $effectiveMinutes);

            $activeRegularTime->update([
                'end_time' => now(),
                'duration_minutes' => $elapsedMinutes,
                'total_price' => $regularTimeCost
            ]);
        }

        // Start Combo Time
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
     * Helper: Get Table Hourly Rate
     */
    private function getTableHourlyRate(Table $table)
    {
        if ($table->table_rate_id) {
            $tableRate = TableRate::find($table->table_rate_id);
            if ($tableRate) {
                return $tableRate->hourly_rate;
            }
        }
        return 50000.00; // Default fallback
    }

    /**
     * Helper: Calculate elapsed minutes
     */
    private function calculateElapsedMinutes($timeUsage)
    {
        $start = Carbon::parse($timeUsage->start_time);
        $end = $timeUsage->end_time ? Carbon::parse($timeUsage->end_time) : now();
        return $start->diffInMinutes($end);
    }
}
