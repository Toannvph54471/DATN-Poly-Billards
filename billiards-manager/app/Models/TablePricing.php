<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TablePricing extends Model
{
    protected $table = 'table_pricing';

    protected $fillable = [
        'category_id',
        'duration_minutes',
        'price_per_hour',
    ];

    protected $casts = [
        'price_per_hour' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Lấy giá cho thời gian chơi
     */
    public function calculatePrice(int $minutes): float
    {
        $hours = $minutes / 60;
        return (float) ($this->price_per_hour * $hours);
    }

    public static function getPriceForCategory($categoryId, $minutes)
    {
        $pricing = self::where('category_id', $categoryId)
            ->orderBy('duration_minutes', 'desc')
            ->first();

        if (!$pricing) {
            return 0;
        }

        return $pricing->calculatePrice($minutes);
    }
}