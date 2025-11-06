<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableRate extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category_id',
        'hourly_rate',
        'max_hours',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
