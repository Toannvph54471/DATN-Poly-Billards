<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComboTimeUsage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'combo_id',
        'bill_id',
        'table_id',
        'start_time',
        'end_time',
        'total_minutes',
        'remaining_minutes',
        'is_expired',
        'extra_minutes_added',
        'extra_charge',
        'warning_sent_at',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'warning_sent_at' => 'datetime',
        'is_expired' => 'boolean',
        'extra_charge' => 'decimal:2',
    ];

    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    // Methods
    public function isExpired(): bool
    {
        return $this->is_expired || $this->remaining_minutes <= 0;
    }

    public function getRealTimeRemaining(): int
    {
        if (!$this->start_time || $this->is_expired) {
            return 0;
        }

        if ($this->end_time !== null) {
            return $this->remaining_minutes;
        }

        $elapsedMinutes = $this->start_time->diffInMinutes(now());
        return max(0, $this->remaining_minutes - $elapsedMinutes);
    }

    public function startSession(): bool
    {
        if ($this->start_time !== null) {
            return false;
        }

        $this->update(['start_time' => now()]);
        return true;
    }

    public function endSession(): bool
    {
        if ($this->start_time === null) {
            return false;
        }

        $elapsedMinutes = $this->start_time->diffInMinutes(now());
        $remaining = max(0, $this->remaining_minutes - $elapsedMinutes);

        $this->update([
            'end_time' => now(),
            'remaining_minutes' => $remaining,
            'is_expired' => $remaining <= 0,
        ]);

        return true;
    }

    public function addMinutes(int $minutes): bool
    {
        if ($this->is_expired) {
            return false;
        }

        $this->update([
            'remaining_minutes' => $this->remaining_minutes + $minutes,
            'extra_minutes_added' => ($this->extra_minutes_added ?? 0) + $minutes,
        ]);

        return true;
    }

    public function calculateExtraCharge(float $pricePerMinute): float
    {
        if ($this->remaining_minutes >= 0) {
            return 0;
        }

        $overMinutes = abs($this->remaining_minutes);
        $charge = $overMinutes * $pricePerMinute;

        $this->update(['extra_charge' => $charge]);
        return $charge;
    }
}
