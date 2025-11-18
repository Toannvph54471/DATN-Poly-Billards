<?php

namespace App\Models;

class Reservation extends BaseModel
{
    // Status constants
    const STATUS_PENDING = 'pending';           // Chờ thanh toán hoặc xác nhận
    const STATUS_CONFIRMED = 'confirmed';       // Đã xác nhận, chờ check-in
    const STATUS_CHECKED_IN = 'checked_in';     // Đã check-in
    const STATUS_COMPLETED = 'completed';       // Hoàn thành
    const STATUS_CANCELLED = 'cancelled';       // Đã hủy
    const STATUS_NO_SHOW = 'no_show';          // Không đến

    // Payment status constants
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_REFUNDED = 'refunded';
    const PAYMENT_FAILED = 'failed';

    // Payment type constants
    const PAYMENT_TYPE_ONLINE = 'online';      // Thanh toán online
    const PAYMENT_TYPE_ONSITE = 'onsite';      // Thanh toán tại quán

    protected $fillable = [
        'reservation_code',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'table_id',
        'reservation_time',
        'end_time',
        'duration',
        'guest_count',
        'note',
        'status',
        'checked_in_at',
        'cancelled_at',
        'cancellation_reason',
        'no_show_at',
        'payment_status',
        'payment_type',
        'payment_gateway',
        'transaction_id',
        'payment_url',
        'payment_completed_at',
        'total_amount',
        'deposit_amount',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'reservation_time' => 'datetime',
        'end_time' => 'datetime',
        'payment_completed_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'no_show_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    // ==================== RELATIONSHIPS ====================

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function bill()
    {
        return $this->hasOne(Bill::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(ReservationStatusHistory::class);
    }

    // ==================== PAYMENT METHODS ====================

    /**
     * Tính tổng tiền bàn theo thời gian đặt
     */
    public function calculateTableAmount(): float
    {
        if (!$this->table_id || !$this->duration) {
            return 0;
        }

        $table = $this->relationLoaded('table') ? $this->table : $this->table()->first();
        if (!$table) {
            return 0;
        }

        return (float) $table->calculatePrice($this->duration);
    }

    /**
     * Cập nhật số tiền
     */
    public function updateAmounts(): void
    {
        $total = $this->calculateTableAmount();
        $this->update(['total_amount' => $total]);
    }

    /**
     * Tạo payment record cho reservation
     */
    public function createPayment(string $paymentMethod): Payment
    {
        return $this->payments()->create([
            'amount' => $this->total_amount,
            'currency' => 'VND',
            'payment_method' => $paymentMethod,
            'payment_type' => Payment::TYPE_FULL,
            'status' => Payment::STATUS_PENDING,
            'transaction_id' => $this->generateTransactionId($paymentMethod),
        ]);
    }

    /**
     * Đánh dấu đã thanh toán
     */
    public function markAsPaid(Payment $payment): void
    {
        $this->update([
            'payment_status' => self::PAYMENT_PAID,
            'payment_gateway' => $payment->payment_method,
            'transaction_id' => $payment->transaction_id,
            'payment_completed_at' => now(),
            'status' => self::STATUS_CONFIRMED,
        ]);
    }

    /**
     * Kiểm tra đã thanh toán chưa
     */
    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    /**
     * Lấy payment đã hoàn thành
     */
    public function getCompletedPayment()
    {
        return $this->payments()
            ->where('status', Payment::STATUS_COMPLETED)
            ->first();
    }

    // ==================== CHECK-IN METHODS ====================

    /**
     * ✅ FIXED: Kiểm tra có thể check-in không
     * Cho phép check-in cả khi chưa thanh toán nếu là onsite payment
     */
    public function canCheckIn(): bool
    {
        // ✅ Với online payment: phải thanh toán trước
        if ($this->payment_type === self::PAYMENT_TYPE_ONLINE && !$this->isPaid()) {
            return false;
        }

        // ✅ Với onsite payment: không cần thanh toán trước
        // Khách sẽ thanh toán sau khi chơi xong

        // Phải ở trạng thái pending hoặc confirmed
        if (!in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED])) {
            return false;
        }

        // Phải trong khung giờ cho phép (30 phút trước - 1 giờ sau)
        $now = now();
        $allowedStart = $this->reservation_time->copy()->subMinutes(30);
        $allowedEnd = $this->reservation_time->copy()->addHours(1);

        return $now->between($allowedStart, $allowedEnd);
    }

    /**
     * Check-in và tạo Bill
     */
    public function checkIn(): Bill
    {
        if (!$this->canCheckIn()) {
            throw new \Exception('Không thể check-in vào lúc này');
        }

        \DB::beginTransaction();
        try {
            // Cập nhật status
            $this->update([
                'status' => self::STATUS_CHECKED_IN,
                'checked_in_at' => now()
            ]);

            // Tạo Bill
            $bill = Bill::create([
                'bill_number' => $this->generateBillNumber(),
                'table_id' => $this->table_id,
                'customer_id' => $this->customer_id,
                'reservation_id' => $this->id,
                'staff_id' => auth()->id() ?? 1,
                'start_time' => now(),
                'status' => Bill::STATUS_OPEN,
                'total_amount' => 0,
                'discount_amount' => 0,
                'final_amount' => 0,
            ]);

            // Tạo time usage
            $bill->billTimeUsages()->create([
                'start_time' => now(),
                'hourly_rate' => $this->table->getHourlyRate(),
            ]);

            // Cập nhật trạng thái bàn
            $this->table->update(['status' => Table::STATUS_OCCUPIED]);

            \DB::commit();
            return $bill;
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    // ==================== STATUS METHODS ====================

    public function cancel($reason = null): bool
    {
        // Chỉ cancel được khi pending hoặc confirmed
        if (!in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED])) {
            return false;
        }

        // Nếu đã thanh toán, cần hoàn tiền
        if ($this->isPaid()) {
            $payment = $this->getCompletedPayment();
            if ($payment) {
                $payment->refund("Hủy đặt bàn: " . $reason);
            }
        }

        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'payment_status' => $this->isPaid() ? self::PAYMENT_REFUNDED : $this->payment_status,
        ]);
    }

    public function markAsNoShow(): bool
    {
        return $this->update([
            'status' => self::STATUS_NO_SHOW,
            'no_show_at' => now()
        ]);
    }

    public function complete(): bool
    {
        return $this->update([
            'status' => self::STATUS_COMPLETED
        ]);
    }

    // ==================== HELPER METHODS ====================

    private function generateTransactionId(string $method): string
    {
        $prefix = strtoupper(substr($method, 0, 3));
        return $prefix . date('YmdHis') . rand(1000, 9999);
    }

    private function generateBillNumber(): string
    {
        return 'BILL-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Chờ xác nhận',
            self::STATUS_CONFIRMED => 'Đã xác nhận',
            self::STATUS_CHECKED_IN => 'Đã check-in',
            self::STATUS_COMPLETED => 'Hoàn thành',
            self::STATUS_CANCELLED => 'Đã hủy',
            self::STATUS_NO_SHOW => 'Không đến',
            default => 'Không xác định',
        };
    }

    public function getPaymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_PENDING => 'Chưa thanh toán',
            self::PAYMENT_PAID => 'Đã thanh toán',
            self::PAYMENT_REFUNDED => 'Đã hoàn tiền',
            self::PAYMENT_FAILED => 'Thanh toán thất bại',
            default => 'Không xác định',
        };
    }

    // ==================== SCOPES ====================

    public function scopeUpcoming($query)
    {
        return $query->where('reservation_time', '>=', now())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('reservation_time', today());
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->reservation_code)) {
                $model->reservation_code = 'RSV' . date('Ymd') . '-' . str_pad(
                    Reservation::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }
}
