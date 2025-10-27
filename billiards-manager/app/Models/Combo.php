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

    // Relationships
    public function comboItems()
    {
        return $this->hasMany(ComboItem::class);
    }
    
        public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_combo');
    }

     public function timeUsages()
    {
        return $this->hasMany(ComboTimeUsage::class);
    }

    public function billDetails()
    {
        return $this->hasMany(BillDetail::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    // Methods
    public function getFinalPrice(): float
    {
        return $this->price;
    }

    public function isActive(): bool
    {
        return $this->status === 'Active';
    }
}
