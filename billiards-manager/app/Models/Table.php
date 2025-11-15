<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\TablePricingService;

class Table extends BaseModel
{
    use HasFactory, SoftDeletes;

    const TYPE_STANDARD = 'standard';
    const TYPE_VIP = 'vip';
    const TYPE_COMPETITION = 'competition';

    const STATUS_AVAILABLE = 'available';
    const STATUS_PAUSED = 'paused';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_RESERVED = 'reserved';
    const STATUS_QUICK = 'quick';

    protected $fillable = [
        'table_number',
        'table_name',
        'capacity',
        'type',
        'status',
        'description',
        'created_by',
        'updated_by'
    ];

    public function tableRate()
    {
        return $this->belongsTo(TableRate::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function currentBill()
    {
        return $this->hasOne(Bill::class)
            ->whereIn('status', ['Open', 'quick'])
            ->where('payment_status', 'Pending')
            ->latest();
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


    // Status Methods
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

    // ============ PRICING METHODS ============

    /**
     * Lấy giá giờ của bàn (từ service)
     * 
     * @param string|null $rateCode - Mã gói giá đặc biệt
     * @return float
     */
    public function getHourlyRate(?string $rateCode = null): float
    {
        return app(TablePricingService::class)->getHourlyRate($this, now(), $rateCode);
    }

    /**
     * Tính giá bàn theo số phút
     */
    public function calculatePrice(int $minutes, ?string $rateCode = null): float
    {
        return app(TablePricingService::class)->calculateTablePrice($this, $minutes, $rateCode);
    }

    /**
     * Lấy thông tin chi tiết về giá
     */
    public function getPricingDetails(int $minutes = 60, ?string $rateCode = null): array
    {
        return app(TablePricingService::class)->getPricingDetails($this, $minutes, $rateCode);
    }

    /**
     * Kiểm tra bàn có hỗ trợ gói giá này không
     */
    public function supportsRateCode(string $rateCode): bool
    {
        $rates = $this->getAvailableRates();

        foreach ($rates as $rate) {
            if ($rate['code'] === $rateCode) {
                return true;
            }
        }

        return false;
    }

    /**
     * DEPRECATED: Dùng getHourlyRate() thay thế
     * Giữ lại để tương thích ngược
     */
    public function hourly_rate(): float
    {
        return $this->getHourlyRate();
    }

    public function rate()
    {
        return $this->hasOneThrough(
            TableRate::class,
            Category::class,
            'id',
            'category_id',
            'category_id',
            'id'
        );
    }
}
