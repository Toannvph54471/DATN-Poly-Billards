<?php

namespace App\Models;

class BillTimeUsage extends BaseModel
{
    protected $fillable = [
        'bill_id',
        'start_time',
        'end_time',
        'duration',
        'rate_per_hour',
        'total_cost',
        'created_by'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration' => 'integer', // in minutes
        'rate_per_hour' => 'decimal:2',
        'total_cost' => 'decimal:2'
    ];

    // Relationships
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    // Methods
    public function calculateCost(): float
    {
        $hours = $this->duration / 60;
        return $hours * $this->rate_per_hour;
    }
}