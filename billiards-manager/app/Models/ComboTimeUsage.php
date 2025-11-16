<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComboTimeUsage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'combo_id',
        'bill_id',
        'table_id',
        'start_time',
        'end_time',
        'total_minutes',
        'remaining_minutes',
        'is_expired'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_expired' => 'boolean'
    ];

    public function combo(): BelongsTo
    {
        return $this->belongsTo(Combo::class);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function isRunning(): bool
    {
        return !$this->is_expired && is_null($this->end_time);
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

    public function pause()
    {
        if (!$this->is_expired) {
            $this->update(['paused_at' => now()]);
        }
    }

    public function resume()
    {
        if ($this->paused_at && !$this->is_expired) {
            $pausedDuration = now()->diffInMinutes($this->paused_at);
            $this->update([
                'paused_at' => null,
                'remaining_minutes' => max(0, $this->remaining_minutes - $pausedDuration)
            ]);

            $this->checkExpiration();
        }
    }

    public function checkExpiration()
    {
        if ($this->is_expired || $this->paused_at) {
            return;
        }

        $elapsedMinutes = now()->diffInMinutes($this->start_time);
        $remaining = $this->total_minutes - $elapsedMinutes;

        if ($remaining <= 0) {
            $this->expire();
        } else {
            $this->update(['remaining_minutes' => $remaining]);
        }
    }

    public function expire()
    {
        $this->update([
            'remaining_minutes' => 0,
            'is_expired' => 1,
            'end_time' => now()
        ]);

        // Chuyển sang tính giờ thường nếu đây là combo cuối cùng
        $this->switchToNormalBilling();
    }

    protected function switchToNormalBilling()
    {
        $bill = $this->bill;

        // Kiểm tra nếu không còn combo active nào
        $hasActiveCombos = $bill->activeComboTimeUsages()
            ->where('id', '!=', $this->id)
            ->exists();

        if (!$hasActiveCombos) {
            // Tạo bill time usage mới với giá thường
            BillTimeUsage::create([
                'bill_id' => $bill->id,
                'start_time' => now(),
                'hourly_rate' => $bill->table->tableRate->hourly_rate,
                'total_price' => 0
            ]);
        }
    }

    public function getRemainingTimeFormatted()
    {
        $minutes = $this->remaining_minutes;
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $mins);
    }
}
