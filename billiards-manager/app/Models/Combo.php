<?php

namespace App\Models;

class Combo extends BaseModel
{
    protected $fillable = [
        'combo_code',
        'name',
        'price',
        'discount_price',
        'image',
        'description',
        'is_available',
        'valid_from',
        'valid_to',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_available' => 'boolean',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime'
    ];

    // Relationships
    public function comboItems()
    {
        return $this->hasMany(ComboItem::class);
    }

    public function billDetails()
    {
        return $this->hasMany(BillDetail::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                    ->where(function($q) {
                        $q->whereNull('valid_from')
                          ->orWhere('valid_from', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('valid_to')
                          ->orWhere('valid_to', '>=', now());
                    });
    }

    // Methods
    public function getFinalPrice(): float
    {
        return $this->discount_price ?: $this->price;
    }

    public function isValid(): bool
    {
        if (!$this->is_available) return false;
        
        $now = now();
        if ($this->valid_from && $this->valid_from > $now) return false;
        if ($this->valid_to && $this->valid_to < $now) return false;
        
        return true;
    }
}