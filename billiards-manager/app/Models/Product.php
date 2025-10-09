<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'product_type',
        'price',
        'cost_price',
        'stock_quantity',
        'min_stock_level',
        'unit',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2'
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
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeDrinks($query)
    {
        return $query->where('category', 'Drink');
    }

    public function scopeFoods($query)
    {
        return $query->where('category', 'Food');
    }

    public function scopeServices($query)
    {
        return $query->where('category', 'Service');
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= min_stock_level');
    }

    // Methods
    public function decreaseStock($quantity)
    {
        $this->decrement('stock_quantity', $quantity);
    }

    public function increaseStock($quantity)
    {
        $this->increment('stock_quantity', $quantity);
    }

    public function isLowStock()
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    public function getProfitMarginAttribute()
    {
        if ($this->cost_price > 0) {
            return (($this->price - $this->cost_price) / $this->cost_price) * 100;
        }
        return 0;
    }
}