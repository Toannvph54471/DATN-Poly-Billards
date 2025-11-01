<?php

namespace App\Models;

class PromotionProduct extends BaseModel
{
    protected $fillable = ['promotion_id', 'product_id'];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
