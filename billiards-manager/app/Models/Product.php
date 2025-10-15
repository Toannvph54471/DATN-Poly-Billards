<?php

namespace App\Models;

class Product extends BaseModel
{
    const TYPE_FOOD = 'food';
    const TYPE_DRINK = 'drink';
    const TYPE_OTHER = 'other';
    const TYPE_SERVICE = 'service';

    protected $fillable = [
        'product_code',
        'name',
        'type',
        'category',
        'price',
        'cost_price',
        'stock_quantity',
        'min_stock',
        'unit',
        'image',
        'description',
        'is_available',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'min_stock' => 'integer',
        'is_available' => 'boolean'
    ];

    // Relationships
    public function billDetails()
    {
        return $this->hasMany(BillDetail::class);
    }

    public function comboItems()
    {
        return $this->hasMany(ComboItem::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                    ->where('stock_quantity', '>', 0);
    }

    public function scopeFood($query)
    {
        return $query->where('type', self::TYPE_FOOD);
    }

    public function scopeDrink($query)
    {
        return $query->where('type', self::TYPE_DRINK);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= min_stock');
    }

    // Methods
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock;
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
        return $this->increment('stock_quantity', $quantity);
    }
}