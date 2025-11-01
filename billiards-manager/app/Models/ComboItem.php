<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComboItem extends Model
{
    protected $fillable = [
        'combo_id',
        'product_id',
        'quantity',
        'is_required',
        'choice_group',
        'max_choices',
    ];

    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Tính giá theo product->price hiện tại
    public function getSubtotal(): float
    {
        return (float) ($this->product->price * $this->quantity);
    }
}