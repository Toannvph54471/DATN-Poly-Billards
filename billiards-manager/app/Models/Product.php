<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model 
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'product_code',
        'name',
        'category_id',      // ĐÃ THAY category (string) → category_id
        'product_type',     // Service, Consumption
        'price',
        'cost_price',
        'stock_quantity',
        'min_stock_level',
        'unit',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'min_stock_level' => 'integer',
    ];

    const STATUS_ACTIVE = 'Active';
    const STATUS_INACTIVE = 'Inactive';

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function billDetails()
    {
        return $this->hasMany(BillDetail::class);
    }

    public function comboItems()
    {
        return $this->hasMany(ComboItem::class);
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_products');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
    }

    // ĐÃ XÓA scopeCategory($category) vì không còn cột category (string)
    // Thay bằng scopeCategoryId()
    public function scopeCategoryId($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeService($query)
    {
        return $query->where('product_type', 'Service');
    }

    public function scopeConsumption($query)
    {
        return $query->where('product_type', 'Consumption');
    }

    // Methods
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_ACTIVE && $this->stock_quantity > 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    public function reduceStock($quantity): bool
    {
        if ($this->stock_quantity >= $quantity) {
            $this->decrement('stock_quantity', $quantity);
            return true;
        }
        return false;
    }

    public function addStock($quantity): bool
    {
        $this->increment('stock_quantity', $quantity);
        return true;
    }

    public function isService(): bool
    {
        return $this->product_type === 'Service';
    }

    public function isConsumption(): bool
    {
        return $this->product_type === 'Consumption';
    }
}
