<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillDetail extends Model
{
    use HasFactory;

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

    public function parent()
    {
        return $this->belongsTo(BillDetail::class, 'parent_bill_detail_id');
    }

    public function components()
    {
        return $this->hasMany(BillDetail::class, 'parent_bill_detail_id');
    }

    // Methods
    public function updateTotalPrice()
    {
        $this->total_price = $this->unit_price * $this->quantity;
        return $this->save();
    }

    public function isCombo()
    {
        return !is_null($this->combo_id);
    }

    public function isProduct()
    {
        return !is_null($this->product_id);
    }
}