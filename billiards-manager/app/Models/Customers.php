<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'customer_type',
        'status', 
        'total_visits',
        'total_spent',
        'last_visit_at',
        'note',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_spent' => 'decimal:2',
        'last_visit_at' => 'datetime',
        'total_visits' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope for active customers
     */
    public function scopeActive($query)
    {
        return $query->where('total_visits', '>', 0);
    }

    /**
     * Scope for VIP customers
     */
    public function scopeVip($query)
    {
        return $query->where('customer_type', 'VIP');
    }

    /**
     * Scope for regular customers
     */
    public function scopeRegular($query)
    {
        return $query->where('customer_type', 'Regular');
    }

    /**
     * Scope for new customers
     */
    public function scopeNew($query)
    {
        return $query->where('customer_type', 'New');
    }

    /**
     * Check if customer is VIP
     */
    public function isVip(): bool
    {
        return $this->customer_type === 'VIP';
    }

    /**
     * Check if customer is new
     */
    public function isNew(): bool
    {
        return $this->customer_type === 'New';
    }

    /**
     * Check if customer is regular
     */
    public function isRegular(): bool
    {
        return $this->customer_type === 'Regular';
    }

    /**
     * Get customer status based on visits and spending
     */
    public function getStatusAttribute(): string
    {
        if ($this->total_visits >= 10 || $this->total_spent >= 5000000) {
            return 'VIP';
        } elseif ($this->total_visits >= 3 || $this->total_spent >= 1000000) {
            return 'Regular';
        } else {
            return 'New';
        }
    }

    /**
     * Format total spent for display
     */
    public function getFormattedTotalSpentAttribute(): string
    {
        return number_format($this->total_spent, 0, ',', '.') . ' VNĐ';
    }

    /**
     * Increment visit count and update last visit
     */
    public function recordVisit(float $amount = 0): void
    {
        $this->increment('total_visits');
        $this->increment('total_spent', $amount);
        $this->last_visit_at = now();
        $this->save();
    }

    /**
     * Update customer type based on activity
     */
    public function updateCustomerType(): void
    {
        $this->customer_type = $this->status;
        $this->save();
    }

    /**
     * Relationship với bills (nếu có)
     */
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Relationship với reservations (nếu có)
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}