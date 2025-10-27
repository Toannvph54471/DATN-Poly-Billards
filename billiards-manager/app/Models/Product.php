<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'product_code',
        'name',
        'category',         // Drink, Food, Service
        'product_type',     // Single, Combo
        'price',
        'cost_price',
        'stock_quantity',
        'min_stock_level',
        'unit',             // Chai, Lon, Giờ
        'status',           // Active, Inactive
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'min_stock_level' => 'integer',
    ];


    const STATUS_ACTIVE = 'Active';
    const STATUS_INACTIVE = 'Inactive';
    const TYPE_SINGLE = 'Single';
    const TYPE_COMBO = 'Combo';
    
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
    
    // 🔍 Scopes


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


    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }


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
}
