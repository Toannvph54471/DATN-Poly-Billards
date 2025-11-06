<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];

    // Relationships
    public function comboItems()
    {
        return $this->hasMany(ComboItem::class, 'combo_id');
    }

    public function promotions()
    {
        return $this->belongsTo(Category::class, 'table_category_id');
    }

    public function timeUsages()
    {
        return $this->hasMany(ComboTimeUsage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTimeCombo($query)
    {
        return $query->where('is_time_combo', true);
    }

    // Validation
    public function hasOnlyOneServiceProduct(): bool
    {
        return $this->comboItems()
            ->whereHas('product', fn($q) => $q->where('product_type', 'Service'))
            ->count() <= 1;
    }

    public function getServiceProduct()
    {
        return $this->comboItems()
            ->whereHas('product', fn($q) => $q->where('product_type', 'Service'))
            ->with('product')
            ->first();
    }

    public function canBeAppliedToTable(Table $table): bool
    {
        if (!$this->is_time_combo) return true;
        return $this->table_category_id === $table->category_id;
    }

    public function getAvailableTables()
    {
        if (!$this->is_time_combo) return collect();

        return Table::where('category_id', $this->table_category_id)
            ->where('status', 'available')
            ->get();
    }

    public function getDiscountAmount(): float
    {
        return max(0, $this->actual_value - $this->price);
    }

    public function getDiscountPercent(): int
    {
        if ($this->actual_value == 0) return 0;
        return round(($this->getDiscountAmount() / $this->actual_value) * 100);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isTimeCombo(): bool
    {
        return $this->is_time_combo;
    }

    public function getCurrentTimeUsage()
    {
        return $this->timeUsages()
            ->where('is_expired', false)
            ->where('remaining_minutes', '>', 0)
            ->whereNull('end_time')
            ->latest('start_time')
            ->first();
    }
}
