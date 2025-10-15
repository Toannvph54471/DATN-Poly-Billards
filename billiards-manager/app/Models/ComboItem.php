<?php

namespace App\Models;

class ComboItem extends BaseModel
{
    protected $fillable = [
        'combo_id',
        'product_id',
        'quantity',
        'unit_price',
        'created_by'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2'
    ];

    // Relationships
    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Methods
    public function getTotalPrice(): float
    {
        return $this->quantity * $this->unit_price;
    }
}