<?php

namespace App\Models;

class Payment extends BaseModel
{
    const METHOD_CASH = 'cash';
    const METHOD_CARD = 'card';
    const METHOD_TRANSFER = 'transfer';
    const METHOD_WALLET = 'wallet';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'payment_code',
        'bill_id',
        'customer_id',
        'amount',
        'method',
        'status',
        'payment_date',
        'reference',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime'
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

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('payment_date', today());
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('method', $method);
    }

    // Methods
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'payment_date' => now()
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}