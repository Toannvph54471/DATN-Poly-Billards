<?php

namespace App\Models;

class BillTimeUsage extends BaseModel
{
    protected $table = 'bill_time_usage';
    protected $fillable = [
        'bill_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'hourly_rate',
        'total_price',
        'created_by',
        'paused_at'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_minutes' => 'integer', // in minutes
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

    public function getDurationMinutesAttribute()
    {
        if (!$this->end_time) return 0;
        return $this->start_time->diffInMinutes($this->end_time);
    }

    
}
