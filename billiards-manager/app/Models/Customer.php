<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'customer_type',
        'total_visits',
        'total_spent',
        'note'
    ];

    protected $casts = [
        'total_spent' => 'decimal:2'
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

    // Scopes
    public function scopeVip($query)
    {
        return $query->where('customer_type', 'VIP');
    }

    public function scopeRegular($query)
    {
        return $query->where('customer_type', 'Regular');
    }

    // Methods
    public function incrementVisits()
    {
        $this->increment('total_visits');
    }

    public function addToTotalSpent($amount)
    {
        $this->increment('total_spent', $amount);
    }

    public function shouldBeVip()
    {
        return $this->total_visits >= 10 || $this->total_spent >= 1000000;
    }

    public function promoteToVip()
    {
        if ($this->shouldBeVip() && $this->customer_type !== 'VIP') {
            $this->update(['customer_type' => 'VIP']);
        }
    }
}