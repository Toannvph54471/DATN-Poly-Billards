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
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_RESERVED = 'reserved';

    protected $fillable = [
        'table_number',
        'table_name',
        'category_id',
        'capacity',
        'type',
        'status',
        'description',
        'position',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        // REMOVED: 'hourly_rate' => 'decimal:2',
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

    // ============ PRICING METHODS - MỚI ============

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
     * Lấy các gói giá có sẵn cho bàn này
     */
    public function getAvailableRates(): array
    {
        if (!$this->category_id) {
            return [];
        }

        return app(TablePricingService::class)->getAvailableRates($this->category_id);
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
}
