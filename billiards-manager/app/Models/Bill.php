<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends BaseModel
{
    const STATUS_OPEN = 'open';
    const STATUS_PAUSED = 'paused';
    const STATUS_CLOSED = 'closed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'bill_number',
        'table_id',
        'user_id',
        'reservation_id',
        'staff_id',
        'start_time',
        'end_time',
        'paused_at',
        'total_amount',
        'discount_amount',
        'final_amount',
        'payment_method',
        'payment_status',
        'is_paid',
        'paid_at',
        'status',
        'note',
        'created_by',
        'updated_by',
        'paused_duration',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'paid_at' => 'datetime',
        'paused_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'paused_duration' => 'integer',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function scopePaidByCurrentStaff($query)
    {
        return $query->where('staff_id', auth()->id())
            ->where('payment_status', 'Paid');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id');
    }

    public function billDetails()
    {
        return $this->hasMany(BillDetail::class);
    }

    
    public function billTimeUsages(): HasMany
    {
        return $this->hasMany(BillTimeUsage::class);
    }

    public function comboTimeUsages()
    {
        return $this->hasMany(ComboTimeUsage::class, 'bill_id');
    }

    // === QUAN HỆ PAYMENT QUA RESERVATION ===
    public function reservationPayments()
    {
        if (!$this->reservation_id) {
            // Trả về collection rỗng nếu không có reservation
            return $this->reservation()->getRelated()->newQuery()->whereNull('id');
        }

        return $this->hasManyThrough(
            Payment::class,
            Reservation::class,
            'id',
            'reservation_id',
            'reservation_id',
            'id'
        );
    }

    public function completedPayments()
    {
        return $this->reservationPayments()->where('status', Payment::STATUS_COMPLETED);
    }

    // === TÍNH TOÁN PAYMENT ===
    public function getTotalPaidAttribute(): float
    {
        if ($this->reservation_id) {
            return Payment::where('reservation_id', $this->reservation_id)
                ->where('status', Payment::STATUS_COMPLETED)
                ->sum('amount');
        }
        return 0;
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->final_amount - $this->total_paid);
    }

    public function isFullyPaid(): bool
    {
        return $this->total_paid >= $this->final_amount;
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_OPEN, self::STATUS_PAUSED]);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    // Status Methods
    public function isOpen(): bool
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_PAUSED]);
    }

    public function isPaid(): bool
    {
        return $this->is_paid === true;
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function canBePaid(): bool
    {
        return $this->status === self::STATUS_CLOSED && !$this->is_paid;
    }

    // Label Methods
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'Đang mở',
            self::STATUS_PAUSED => 'Tạm dừng',
            self::STATUS_CLOSED => 'Đã đóng',
            self::STATUS_CANCELLED => 'Đã hủy',
            default => 'Không xác định'
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        if ($this->is_paid) {
            return 'Đã thanh toán';
        }

        if ($this->total_paid > 0) {
            return 'Thanh toán một phần';
        }

        return 'Chưa thanh toán';
    }
}
