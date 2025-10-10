<?php

namespace App\Models;

class Bill extends BaseModel
{
    const STATUS_OPEN = 'open';
    const STATUS_PLAYING = 'playing';
    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_CASH = 'cash';
    const PAYMENT_CARD = 'card';
    const PAYMENT_TRANSFER = 'transfer';
    const PAYMENT_WALLET = 'wallet';

    protected $fillable = [
        'bill_code',
        'customer_id',
        'table_id',
        'employee_id',
        'start_time',
        'end_time',
        'total_time',
        'table_price',
        'product_total',
        'total_amount',
        'discount',
        'final_amount',
        'payment_method',
        'payment_status',
        'status',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'table_price' => 'decimal:2',
        'product_total' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'total_time' => 'integer' // in minutes
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function billDetails()
    {
        return $this->hasMany(BillDetail::class);
    }

    public function billTimeUsages()
    {
        return $this->hasMany(BillTimeUsage::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_OPEN, self::STATUS_PLAYING]);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Methods
    public function isOpen(): bool
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_PLAYING]);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function calculateTotalTime(): int
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }
        return $this->start_time->diffInMinutes($this->end_time);
    }

    public function calculateTableCharge(): float
    {
        $totalTime = $this->calculateTotalTime();
        $hours = ceil($totalTime / 60);
        return $hours * $this->table_price;
    }

    public function markAsPaid(): bool
    {
        return $this->update([
            'status' => self::STATUS_PAID,
            'payment_status' => self::STATUS_PAID
        ]);
    }
}