<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_number',
        'table_id',
        'user_id',
        'reservation_id',
        'staff_id',
        'start_time',
        'end_time',
        'total_amount',
        'discount_amount',
        'final_amount',
        'payment_method',
        'payment_status',
        'status',
        'paused_duration',
        'note'
    ];


    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];


    public function timeUsages()
    {
        return $this->hasMany(BillTimeUsage::class);
    }


    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function billDetails(): HasMany
    {
        return $this->hasMany(BillDetail::class);
    }

    public function billTimeUsages(): HasMany
    {
        return $this->hasMany(BillTimeUsage::class);
    }

    public function comboTimeUsages(): HasMany
    {
        return $this->hasMany(ComboTimeUsage::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'Open');
    }

    public function scopeQuick($query)
    {
        return $query->where('status', 'quick');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'Paid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'Pending');
    }

    // Method Time 
    public function pauseTimeTracking()
    {
        $activeUsage = $this->activeTimeUsage;

        if ($activeUsage) {
            $activeUsage->update([
                'paused_at' => time(),
                'updated_at' => now()
            ]);
        }

        // Pause active combo time usages
        $this->activeComboTimeUsages->each->pause();

        return true;
    }

    public function resumeTimeTracking()
    {
        $activeUsage = $this->activeTimeUsage;

        if ($activeUsage && $activeUsage->paused_at) {
            $pausedDuration = time() - $activeUsage->paused_at;

            $activeUsage->update([
                'paused_duration' => $activeUsage->paused_duration + $pausedDuration,
                'paused_at' => null,
                'updated_at' => now()
            ]);
        }

        // Resume active combo time usages
        $this->activeComboTimeUsages->each->resume();

        return true;
    }

    public function getTotalPausedDuration()
    {
        return $this->timeUsages->sum('paused_duration');
    }

    public function getActivePlayDuration()
    {
        $activeUsage = $this->activeTimeUsage;
        if (!$activeUsage) return 0;

        $startTime = $activeUsage->start_time->timestamp;
        $now = time();
        $pausedDuration = $activeUsage->paused_duration;

        if ($activeUsage->paused_at) {
            $pausedDuration += ($now - $activeUsage->paused_at);
        }

        return max(0, $now - $startTime - $pausedDuration);
    }

    public function checkAndExpireCombos()
    {
        $this->activeComboTimeUsages->each->checkExpiration();
    }
}
