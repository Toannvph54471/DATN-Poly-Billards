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
        'check_in',
        'check_out',
        'actual_hours',
        'notes',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'shift_date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'actual_hours' => 'decimal:2'
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
            'check_in' => now(),
            'status' => self::STATUS_ACTIVE
        ]);
    }

    public function checkOut(): bool
    {
        $actualHours = $this->check_in ? $this->check_in->diffInHours(now()) : 0;

        return $this->update([
            'check_out' => now(),
            'actual_hours' => $actualHours,
            'status' => self::STATUS_COMPLETED
        ]);
    }

    public function isCheckedIn(): bool
    {
        return !is_null($this->check_in) && is_null($this->check_out);
    }
}
