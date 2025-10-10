<?php

namespace App\Models;

class Table extends BaseModel
{
    const TYPE_STANDARD = 'standard';
    const TYPE_VIP = 'vip';
    const TYPE_COMPETITION = 'competition';

    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_RESERVED = 'reserved';

    protected $fillable = [
        'table_number',
        'table_name', 
        'type',
        'status',
        'hourly_rate',
        'description',
        'position',
        'created_by',
        'updated_by'
    ];

    // Relationships
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function currentBill()
    {
        return $this->hasOne(Bill::class)->ofMany([
            'id' => 'max',
        ], function ($query) {
            $query->whereIn('status', [Bill::STATUS_OPEN, Bill::STATUS_PLAYING]);
        });
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', self::STATUS_OCCUPIED);
    }

    // Methods
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function markAsOccupied(): bool
    {
        return $this->update(['status' => self::STATUS_OCCUPIED]);
    }

    public function markAsAvailable(): bool
    {
        return $this->update(['status' => self::STATUS_AVAILABLE]);
    }
}