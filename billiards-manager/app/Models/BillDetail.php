<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class BillDetail extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'bill_id',
        'product_id',
        'combo_id',
        'quantity',
        'unit_price',
        'original_price',
        'total_price',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'integer'
    ];

    // Relationships
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }
}
