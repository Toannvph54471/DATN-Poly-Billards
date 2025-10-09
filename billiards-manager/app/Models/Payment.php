<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

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
        'paid_at' => 'datetime'
    ];

    // Relationships
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'Success');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Methods
    public function markAsSuccess()
    {
        $this->update([
            'status' => 'Success',
            'paid_at' => now()
        ]);

        // Update bill payment status
        $this->bill->update(['payment_status' => 'Paid']);
    }

    public function markAsFailed()
    {
        $this->update(['status' => 'Failed']);
    }
}