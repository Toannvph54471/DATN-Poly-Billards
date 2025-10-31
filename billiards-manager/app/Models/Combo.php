<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Combo extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'combo_code',
        'name',
        'description',
        'price',
        'actual_value',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'actual_value' => 'decimal:2',
    ];

    public function comboItems()
    {
        return $this->hasMany(ComboItem::class);
    }

    public function billDetails()
    {
        return $this->hasMany(BillDetail::class);
    }

    // SỬA: dùng chữ thường
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getFinalPrice(): float
    {
        return $this->price;
    }

    // SỬA: dùng chữ thường
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
