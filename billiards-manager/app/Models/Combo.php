<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Combo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'combo_code',
        'name',
        'description',
        'price',
        'actual_value',
        'status',
        'is_time_combo',
        'play_duration_minutes',
        'table_rate_id', 
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'actual_value' => 'decimal:2',
        'is_time_combo' => 'boolean',
        'play_duration_minutes' => 'integer',
    ];

    // ============ RELATIONSHIPS ============

    public function comboItems(): HasMany
    {
        return $this->hasMany(ComboItem::class, 'combo_id');
    }

    public function tableRate(): BelongsTo
    {
        return $this->belongsTo(TableRate::class, 'table_rate_id');
    }

    public function timeUsages(): HasMany
    {
        return $this->hasMany(ComboTimeUsage::class);
    }

    public function billDetails(): HasMany
    {
        return $this->hasMany(BillDetail::class);
    }

    // ============ SCOPES ============

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTimeCombo($query)
    {
        return $query->where('is_time_combo', true);
    }

    public function scopeRegular($query)
    {
        return $query->where('is_time_combo', false);
    }

    // ============ PRICING METHODS ============

    /**
     * Tính tổng giá trị thực của combo
     */
    public function calculateActualValue(): float
    {
        $total = 0;

        // Giá sản phẩm tiêu dùng
        $total += $this->getProductsTotal();

        // Giá bàn (nếu là combo thời gian)
        if ($this->is_time_combo) {
            $total += $this->getTablePrice();
        }

        // Làm tròn tổng lên hàng nghìn
        return ceil($total / 1000) * 1000;
    }

    /**
     * Tính giá bàn cho combo thời gian - SỬ DỤNG TableRate TRỰC TIẾP
     */
    public function getTablePrice(): float
    {
        if (!$this->is_time_combo || !$this->table_rate_id || !$this->play_duration_minutes) {
            return 0;
        }

        $tableRate = $this->tableRate;
        if (!$tableRate) {
            return 0;
        }

        $hourlyRate = $tableRate->hourly_rate;
        $hours = $this->play_duration_minutes / 60;

        // Tính và làm tròn lên hàng nghìn
        $tablePrice = $hourlyRate * $hours;
        return ceil($tablePrice / 1000) * 1000;
    }

    /**
     * Tính tổng giá sản phẩm tiêu dùng
     */
    public function getProductsTotal(): float
    {
        return $this->comboItems()
            ->get()
            ->sum(fn($item) => $item->unit_price * $item->quantity);
    }

    /**
     * Tính số tiền khách tiết kiệm
     */
    public function getDiscountAmount(): float
    {
        return max(0, $this->actual_value - $this->price);
    }

    /**
     * Tính phần trăm chiết khấu
     */
    public function getDiscountPercent(): int
    {
        if ($this->actual_value == 0) return 0;
        return round(($this->getDiscountAmount() / $this->actual_value) * 100);
    }

    /**
     * Lấy thông tin chi tiết về giá
     */
    public function getPricingDetails(): array
    {
        return [
            'products_total' => $this->getProductsTotal(),
            'table_price' => $this->getTablePrice(),
            'actual_value' => (float) $this->actual_value,
            'price' => (float) $this->price,
            'discount_amount' => $this->getDiscountAmount(),
            'discount_percent' => $this->getDiscountPercent(),
            'is_profitable' => $this->getDiscountAmount() > 0,
        ];
    }

    // ============ STATUS METHODS ============

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isTimeCombo(): bool
    {
        return $this->is_time_combo;
    }

    public function hasActiveSession(): bool
    {
        if (!$this->is_time_combo) {
            return false;
        }

        return $this->timeUsages()
            ->where('is_expired', false)
            ->where('remaining_minutes', '>', 0)
            ->whereNull('end_time')
            ->exists();
    }

    public function getCurrentTimeUsage()
    {
        if (!$this->is_time_combo) {
            return null;
        }

        return $this->timeUsages()
            ->where('is_expired', false)
            ->where('remaining_minutes', '>', 0)
            ->whereNull('end_time')
            ->latest('start_time')
            ->first();
    }

    public function getFormattedDuration(): string
    {
        if (!$this->play_duration_minutes) {
            return 'N/A';
        }

        $hours = floor($this->play_duration_minutes / 60);
        $minutes = $this->play_duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} giờ {$minutes} phút";
        } elseif ($hours > 0) {
            return "{$hours} giờ";
        } else {
            return "{$minutes} phút";
        }
    }

    /**
     * Lấy thông tin đầy đủ về combo
     */
    public function getDetailedInfo(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->combo_code,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'is_active' => $this->isActive(),
            'is_time_combo' => $this->is_time_combo,
            'play_duration_minutes' => $this->play_duration_minutes,
            'table_rate' => $this->tableRate?->name,
            'table_rate_code' => $this->tableRate?->code,
            'hourly_rate' => $this->tableRate?->hourly_rate,
            'pricing' => $this->getPricingDetails(),
            'items_count' => $this->comboItems()->count(),
            'products' => $this->comboItems->map(fn($item) => [
                'name' => $item->product->name ?? 'N/A',
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'subtotal' => (float) ($item->unit_price * $item->quantity),
            ]),
            'has_active_session' => $this->hasActiveSession(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
