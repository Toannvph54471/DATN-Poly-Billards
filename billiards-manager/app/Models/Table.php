<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $table = 'tables';

    protected $fillable = [
        'table_number',
        'type',
        'status',
        'hourly_rate'
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2'
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

    public function currentBill()
    {
        return $this->hasOne(Bill::class)->where('status', 'Open');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'Available');
    }

    public function scopeInUse($query)
    {
        return $query->where('status', 'InUse');
    }

    public function scopeVip($query)
    {
        return $query->where('type', 'VIP');
    }

    public function scopeRegular($query)
    {
        return $query->where('type', 'Regular');
    }

    // Methods
    public function isAvailable()
    {
        return $this->status === 'Available';
    }

    public function isInUse()
    {
        return $this->status === 'InUse';
    }

    public function markAsInUse()
    {
        $this->update(['status' => 'InUse']);
    }

    public function markAsAvailable()
    {
        $this->update(['status' => 'Available']);
    }
}