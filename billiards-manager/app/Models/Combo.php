<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\TablePricingService;

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
        'table_category_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'actual_value' => 'decimal:2',
        'is_time_combo' => 'boolean',
        'play_duration_minutes' => 'integer',
    ];

    // ============ RELATIONSHIPS ============

    public function items(): HasMany
    {
        return $this->hasMany(ComboItem::class);
    }

    public function tableCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'table_category_id');
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
        $total += $this->comboItems()
            ->get()
            ->sum(fn($item) => $item->unit_price * $item->quantity);

        // Giá bàn (nếu là combo thời gian)
        if ($this->is_time_combo && $this->table_category_id && $this->play_duration_minutes) {
            $tablePrice = $this->calculateTablePriceViaService();
            $total += $tablePrice;
        }

        // Làm tròn tổng lên hàng nghìn
        return ceil($total / 1000) * 1000;
    }

    /**
     * Tính giá bàn cho combo thời gian
     */
    public function getTablePrice(): float
    {
        if (!$this->is_time_combo || !$this->table_category_id || !$this->play_duration_minutes) {
            return 0;
        }

        return $this->calculateTablePriceViaService();
    }

    /**
     * Helper: Tính giá bàn qua TablePricingService (dùng hourly_rate mặc định)
     */
    private function calculateTablePriceViaService(): float
    {
        $pricingService = app(TablePricingService::class);

        $category = $this->tableCategory;
        if (!$category) {
            return 0;
        }

        $fakeTable = new Table([
            'category_id' => $this->table_category_id,
        ]);
        $fakeTable->setRelation('category', $category);

        // Dùng hourly_rate mặc định của category (không cần rate_code)
        return $pricingService->calculateTablePrice(
            $fakeTable,
            $this->play_duration_minutes,
            null // Không truyền rate_code
        );
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

    public function getActiveTimeUsages()
    {
        if (!$this->is_time_combo) {
            return collect();
        }

        return $this->timeUsages()
            ->where('is_expired', false)
            ->where('remaining_minutes', '>', 0)
            ->orderBy('start_time', 'desc')
            ->get();
    }

    public function canBeAppliedToTable(Table $table): bool
    {
        if (!$this->is_time_combo) {
            return true;
        }

        return $this->table_category_id === $table->category_id;
    }

    public function getAvailableTables()
    {
        if (!$this->is_time_combo || !$this->table_category_id) {
            return collect();
        }

        return Table::where('category_id', $this->table_category_id)
            ->where('status', 'available')
            ->get();
    }

    // ============ INFO METHODS ============

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
            'table_category' => $this->tableCategory?->name,
            'pricing' => $this->getPricingDetails(),
            'items_count' => $this->comboItems()->count(),
            'products' => $this->comboItems->map(fn($item) => [
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'subtotal' => (float) ($item->unit_price * $item->quantity),
            ]),
            'has_active_session' => $this->hasActiveSession(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function isValid(): array
    {
        $errors = [];

        if (empty($this->name)) {
            $errors[] = 'Combo phải có tên';
        }

        if (empty($this->combo_code)) {
            $errors[] = 'Combo phải có mã';
        }

        if ($this->comboItems()->count() == 0) {
            $errors[] = 'Combo phải có ít nhất 1 sản phẩm';
        }

        if ($this->is_time_combo) {
            if (!$this->table_category_id) {
                $errors[] = 'Combo bàn phải có loại bàn';
            }

            if (!$this->play_duration_minutes) {
                $errors[] = 'Combo bàn phải có thời gian chơi';
            }
        }

        if ($this->price > $this->actual_value) {
            $errors[] = 'Giá bán không được vượt quá giá trị thực (' . number_format($this->actual_value) . 'đ)';
        }

        return [
            'is_valid' => count($errors) === 0,
            'errors' => $errors,
        ];
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
}
