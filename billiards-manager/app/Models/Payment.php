<?php

namespace App\Models;

class Payment extends BaseModel
{
    const METHOD_CASH = 'cash';
    const METHOD_CARD = 'card';
    const METHOD_TRANSFER = 'transfer';
    const METHOD_WALLET = 'wallet';

    // SỬA LẠI CONST STATUS cho khớp với controller
    const STATUS_PENDING = 'Pending';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_FAILED = 'Failed';
    const STATUS_REFUNDED = 'Refunded';

    protected $fillable = [
        'bill_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Scopes - SỬA LẠI cho khớp
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('paid_at', today()); // SỬA thành paid_at
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method); // SỬA thành payment_method
    }

    // Methods
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'paid_at' => now() // SỬA thành paid_at
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
