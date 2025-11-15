<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillTimeUsage extends Model
{
    use HasFactory;
    protected $table = 'bill_time_usage';
    protected $fillable = [
        'bill_id',
        'start_time',
        'end_time',
        'paused_at',
        'paused_duration',
        'duration_minutes',
        'hourly_rate',
        'total_price',
        'created_by',
        'paused_at'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'hourly_rate' => 'decimal:2',
        'total_price' => 'decimal:2',
        'paused_at' => 'integer'
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    // Helper methods
    public function isRunning(): bool
    {
        return is_null($this->end_time) && is_null($this->paused_at);
    }

    public function isPaused(): bool
    {
        return !is_null($this->paused_at);
    }

    public function getElapsedMinutes(): float
    {
        if ($this->isRunning()) {
            return $this->start_time->diffInMinutes(now());
        } elseif ($this->isPaused()) {
            return $this->duration_minutes ?? 0;
        } else {
            return $this->duration_minutes ?? 0;
        }
    }
}
