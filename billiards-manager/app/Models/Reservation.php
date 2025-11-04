<?php

namespace App\Models;

class Reservation extends BaseModel
{
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CHECKED_IN = 'checked_in';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_NO_SHOW = 'no_show'; // Thêm trạng thái mới

    protected $fillable = [
        'reservation_code',
        'customer_id',
        'table_id',
        'reservation_time', // Thay cho reservation_date và start_time
        'end_time',
        'duration',
        'guest_count', // Thay cho number_of_people
        'note', // Thay cho special_requests
        'status',
        'checked_in_at', // Thay cho checked_in_time
        'cancelled_at',
        'cancellation_reason',
        'no_show_at',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'reservation_time' => 'datetime',
        'end_time' => 'datetime',
        'checked_in_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'no_show_at' => 'datetime',
        'duration' => 'integer',
        'guest_count' => 'integer'
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function bill()
    {
        return $this->hasOne(Bill::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(ReservationStatusHistory::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('reservation_time', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('reservation_time', '>=', now())
                    ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    // Methods
    public function checkIn(): bool
    {
        return $this->update([
            'status' => self::STATUS_CHECKED_IN,
            'checked_in_at' => now()
        ]);
    }

    public function cancel($reason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason
        ]);
    }

    public function markAsNoShow(): bool
    {
        return $this->update([
            'status' => self::STATUS_NO_SHOW,
            'no_show_at' => now()
        ]);
    }

    public function isUpcoming(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]) &&
               $this->reservation_time >= now();
    }

    // Tự động tạo reservation_code khi tạo mới
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->reservation_code)) {
                $model->reservation_code = 'RSV' . date('YmdHis') . rand(100, 999);
            }
        });
    }
}