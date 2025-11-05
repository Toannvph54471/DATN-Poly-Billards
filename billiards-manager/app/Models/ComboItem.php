<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ComboItem extends BaseModel
{
    use SoftDeletes;

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
        return $this->belongsTo(Combo::class, 'combo_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Methods
    public function getTotalPrice(): float
    {
        return $this->quantity * $this->unit_price;
    }
}
