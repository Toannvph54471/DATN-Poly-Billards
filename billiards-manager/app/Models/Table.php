<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends BaseModel
{
    use HasFactory, SoftDeletes;

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
        'category_id', // ĐÃ CÓ
        'capacity',    // ĐÃ CÓ từ migration
        'type',
        'status',
        'hourly_rate',
        'description',
        'position',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

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

    public function scopeCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
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

    public function getCategoryName(): string
    {
        return $this->category?->name ?? 'Unknown';
    }
}
