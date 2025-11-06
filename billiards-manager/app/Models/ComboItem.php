<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComboItem extends Model
{
    use SoftDeletes;

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
        return $this->belongsTo(Combo::class, 'combo_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Tính giá theo product->price hiện tại
    public function getSubtotal(): float
    {
        return (float) ($this->product->price * $this->quantity);
    }
}
