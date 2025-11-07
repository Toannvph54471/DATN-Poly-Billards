<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComboItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'combo_id',
        'product_id',
        'quantity',
        'unit_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    // ============ RELATIONSHIPS ============

    public function combo(): BelongsTo
    {
        return $this->belongsTo(Combo::class, 'combo_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // ============ CALCULATION METHODS ============

    /**
     * Tính giá sản phẩm: giá × số lượng
     */
    public function getPrice(): float
    {
        return round($this->unit_price * $this->quantity, 2);
    }

    /**
     * Tính subtotal (alias của getPrice)
     */
    public function getSubtotal(): float
    {
        return $this->getPrice();
    }

    /**
     * Lấy tên hiển thị
     */
    public function getDisplayName(): string
    {
        return $this->product->name;
    }

    /**
     * Lấy thông tin chi tiết
     */
    public function getDetailedInfo(): array
    {
        return [
            'product_id' => $this->product_id,
            'product_name' => $this->product->name,
            'product_code' => $this->product->product_code,
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'subtotal' => $this->getSubtotal(),
        ];
    }
}
