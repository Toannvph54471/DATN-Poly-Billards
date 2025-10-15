<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class BaseModel extends Model
{
    use HasFactory, SoftDeletes;

    // ========== COMMON STATUS CONSTANTS ==========
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_PAID = 'paid';
    const STATUS_UNPAID = 'unpaid';

    // ========== COMMON CASTS ==========
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'amount' => 'decimal:2',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    // ========== COMMON FILLABLE ==========
    protected $fillable = [
        'created_by',
        'updated_by',
        'status',
        'notes'
    ];

    // ========== COMMON SCOPES ==========
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // ========== COMMON METHODS ==========
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function markAsActive(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    public function markAsCompleted(): bool
    {
        return $this->update(['status' => self::STATUS_COMPLETED]);
    }

    public function markAsCancelled(): bool
    {
        return $this->update(['status' => self::STATUS_CANCELLED]);
    }

    // ========== TIMESTAMP HELPERS ==========
    public function getCreatedDateAttribute(): string
    {
        return $this->created_at->format('d/m/Y');
    }

    public function getCreatedTimeAttribute(): string
    {
        return $this->created_at->format('H:i:s');
    }

    public function getUpdatedDateAttribute(): string
    {
        return $this->updated_at->format('d/m/Y');
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }
}