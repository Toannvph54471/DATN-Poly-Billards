<?php

namespace App\Models;

class Promotion extends BaseModel
{
    protected $fillable = [
        'promotion_code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'applies_to_combo',
        'applies_to_time_combo',
        'min_play_minutes',
        'status',
    ];

    public function combos()
    {
        return $this->belongsToMany(Combo::class, 'promotion_combo');
    }

        public function products()
    {
        return $this->belongsToMany(Product::class, 'promotion_products');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && now()->between($this->start_date, $this->end_date);
    }
}
