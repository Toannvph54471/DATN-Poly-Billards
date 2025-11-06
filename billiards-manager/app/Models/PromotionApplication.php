<?php

namespace App\Models;

class PromotionApplication extends BaseModel
{
    protected $fillable = [
        'bill_id',
        'promotion_id',
        'applied_discount'
    ];

    protected $casts = [
        'applied_discount' => 'decimal:2'
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}