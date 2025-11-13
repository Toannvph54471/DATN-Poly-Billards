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

    public function isPaused(): bool
    {
        return !$this->is_expired && !is_null($this->end_time);
    }
}
