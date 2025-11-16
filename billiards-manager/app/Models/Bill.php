<?php

namespace App\Models;

class Bill extends BaseModel
{
    const STATUS_OPEN = 'open';
    const STATUS_PAUSED = 'paused';
    const STATUS_CLOSED = 'closed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'bill_number',
        'customer_id',
        'reservation_id',
        'table_id',
        'staff_id',
        'start_time',
        'end_time',
        'paused_at',
        'total_amount',
        'discount_amount',
        'final_amount',
        'payment_method',
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

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function billDetails()
    {
        return $this->hasMany(BillDetail::class);
    }

    public function billTimeUsages()
    {
        return $this->hasMany(BillTimeUsage::class);
    }

    // *** THÊM MỚI: Payment relationship ***
    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function completedPayments()
    {
        return $this->payments()->where('status', Payment::STATUS_COMPLETED);
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

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
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

    // *** THÊM MỚI: Payment calculation methods ***
    public function getTotalPaidAttribute(): float
    {
        return $this->completedPayments()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->final_amount - $this->total_paid);
    }

    public function isFullyPaid(): bool
    {
        return $this->total_paid >= $this->final_amount;
    }

    // Calculate Methods
    public function calculateTotalTime(): int
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }
        return $this->start_time->diffInMinutes($this->end_time);
    }

    public function getTotalTimeAttribute(): int
    {
        return $this->calculateTotalTime();
    }

    public function getProductTotalAttribute(): float
    {
        return $this->billDetails()->sum('total_price');
    }

    public function getTablePriceAttribute(): float
    {
        return $this->billTimeUsages()->sum('total_price');
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
