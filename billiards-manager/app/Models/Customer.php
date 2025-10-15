<?php

namespace App\Models;

class Customer extends BaseModel
{
    const MEMBERSHIP_REGULAR = 'regular';
    const MEMBERSHIP_VIP = 'vip';
    const MEMBERSHIP_PREMIUM = 'premium';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'membership_type',
        'balance',
        'point',
        'total_spent',
        'visit_count',
        'last_visit',
        'created_by',
        'updated_by'
    ];

    // Relationships
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeVip($query)
    {
        return $query->where('membership_type', self::MEMBERSHIP_VIP);
    }

    public function scopeByPhone($query, $phone)
    {
        return $query->where('phone', $phone);
    }

    // Methods
    public function isVip(): bool
    {
        return $this->membership_type === self::MEMBERSHIP_VIP;
    }

    public function addBalance($amount): bool
    {
        return $this->increment('balance', $amount);
    }

    public function deductBalance($amount): bool
    {
        if ($this->balance >= $amount) {
            return $this->decrement('balance', $amount);
        }
        return false;
    }
}