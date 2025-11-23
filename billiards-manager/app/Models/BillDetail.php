<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bill_id',
        'product_id',
        'combo_id',
        'parent_bill_detail_id',
        'quantity',
        'unit_price',
        'original_price',
        'total_price',
        'is_combo_component'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'is_combo_component' => 'boolean'
    ];

    // Relationships
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function combo(): BelongsTo
    {
        return $this->belongsTo(Combo::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(BillDetail::class, 'parent_bill_detail_id');
    }

    public function components()
    {
        return $this->hasMany(BillDetail::class, 'parent_bill_detail_id');
    }

    public function children()
    {
        return $this->hasMany(BillDetail::class, 'parent_bill_detail_id');
    }
}
