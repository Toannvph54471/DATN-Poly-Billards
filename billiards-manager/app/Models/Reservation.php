<?php

namespace App\Models;

class Reservation extends BaseModel
{
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CHECKED_IN = 'checked_in';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'reservation_code',
        'customer_id',
        'table_id',
        'reservation_date',
        'start_time',
        'end_time',
        'number_of_people',
        'special_requests',
        'deposit_amount',
        'status',
        'checked_in_time',
        'checked_out_time',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'checked_in_time' => 'datetime',
        'checked_out_time' => 'datetime',
        'deposit_amount' => 'decimal:2',
        'number_of_people' => 'integer'
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

    public function bill()
    {
        return $this->hasOne(Bill::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('reservation_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('reservation_date', '>=', today())
                    ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    // Methods
    public function checkIn(): bool
    {
        return $this->update([
            'status' => self::STATUS_CHECKED_IN,
            'checked_in_time' => now()
        ]);
    }

    public function isUpcoming(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]) &&
               $this->reservation_date >= today();
    }
}