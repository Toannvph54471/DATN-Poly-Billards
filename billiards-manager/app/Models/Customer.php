<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends BaseModel
{
    const TYPE_REGULAR = 'regular';
    const TYPE_VIP = 'vip';
    const TYPE_PREMIUM = 'premium';
    
    protected $fillable = [
        'name',
        'phone',
        'email',
        'customer_type',
        'total_visits',
        'total_spent',
        'last_visit_at',
        'note'
    ];

    protected $casts = [
        'last_visit_at' => 'datetime',
        'total_spent' => 'decimal:2',
    ];

    // Các phương thức quan hệ nếu có
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    // Các phương thức tiện ích
    public function updateVisitInfo($amount)
    {
        $this->total_visits += 1;
        $this->total_spent += $amount;
        $this->last_visit_at = now();
        $this->save();
    }

    public function isVip()
    {
        return $this->customer_type === 'VIP';
    }

    // Scope để lấy khách hàng theo loại
    public function scopeVip($query)
    {
        return $query->where('customer_type', 'VIP');
    }

    public function scopeRegular($query)
    {
        return $query->where('customer_type', 'Regular');
    }

    public function scopeNew($query)
    {
        return $query->where('customer_type', 'New');
    }
}