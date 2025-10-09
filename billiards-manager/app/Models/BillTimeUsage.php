<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillTimeUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'hourly_rate',
        'total_price'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'hourly_rate' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    // Relationships
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    // Methods
    public function calculatePrice()
    {
        $hours = $this->duration_minutes / 60;
        $this->total_price = $hours * $this->hourly_rate;
        return $this->save();
    }

    public function getDurationInHours()
    {
        return $this->duration_minutes / 60;
    }
}