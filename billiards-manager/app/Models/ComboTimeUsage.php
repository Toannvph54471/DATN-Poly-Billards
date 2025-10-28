<?php

namespace App\Models;

use Illuminate\Database\Eloquent\BaseModel;

class ComboTimeUsage extends BaseModel
{
    protected $fillable = [
        'combo_id',
        'bill_id',
        'start_time',
        'end_time',
        'total_minutes',
        'remaining_minutes',
        'is_expired',
    ];

    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    // Helper method
    public function isExpired(): bool
    {
        return $this->is_expired || $this->remaining_minutes <= 0;
    }

}
