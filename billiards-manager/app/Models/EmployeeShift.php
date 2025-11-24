<?php

namespace App\Models;

class EmployeeShift extends BaseModel
{
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'employee_id',
        'shift_id',
        'shift_date',
        'actual_start_time',
        'actual_end_time',
        'total_hours',
        'notes',
        'status',
        'confirmed_by',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'shift_date' => 'date',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'total_hours' => 'decimal:2'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('shift_date', today());
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // Methods
    public function checkIn(): bool
    {
        return $this->update([
            'actual_start_time' => now(),
            'status' => self::STATUS_ACTIVE
        ]);
    }

    public function checkOut(): bool
    {
        // Calculate precise hours (e.g., 1.5 hours)
        $actualHours = $this->actual_start_time ? $this->actual_start_time->floatDiffInHours(now()) : 0;

        return $this->update([
            'actual_end_time' => now(),
            'total_hours' => $actualHours,
            'status' => self::STATUS_COMPLETED
        ]);
    }

    public function isCheckedIn(): bool
    {
        return !is_null($this->actual_start_time) && is_null($this->actual_end_time);
    }
}
