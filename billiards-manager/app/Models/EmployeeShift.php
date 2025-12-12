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
        'updated_by',
        'is_locked'
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

    public function getRealTimeStatusAttribute()
    {
        if (!$this->shift) return 'Chưa xác định';
        
        $shiftDate = $this->shift_date instanceof \Carbon\Carbon ? $this->shift_date : \Carbon\Carbon::parse($this->shift_date);
        $start = \Carbon\Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $this->shift->start_time);
        $end = \Carbon\Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $this->shift->end_time);
        
        if ($end->lt($start)) {
            $end->addDay();
        }
        
        $now = now();
        
        // Find Attendance safely
        $attendance = \App\Models\Attendance::where('employee_id', $this->employee_id)
            ->whereDate('check_in', $shiftDate)
            ->first();

        // 1. Not Checked In
        if (!$attendance) {
            if ($now->gt($end)) return 'Vắng mặt';
            
            $lateThreshold = $start->copy()->addMinutes(15);
            if ($now->gt($lateThreshold)) return 'Đi muộn'; // Late arrival pending
            
            return 'Chưa checkin';
        }
        
        // 2. Checked Out
        if ($attendance->check_out) {
            return 'Đã checkout';
        }
        
        // 3. Checked In (Active)
        
        // Check for "Forgot Checkout" (End + 30 mins buffer)
        if ($now->gt($end->copy()->addMinutes(30))) {
            return 'Tan ca nhưng quên checkout';
        }
        
        // Check for Late Checkin (Only if late_minutes > 0 in DB or calculated)
        // Relying on DB late_minutes is better if available, but calculation is fine
        $checkInTime = \Carbon\Carbon::parse($attendance->check_in);
        if ($checkInTime->gt($start->copy()->addMinutes(15))) {
            return 'Đi muộn';
        }
        
        // Ensure we catch all active cases
        return 'Đang trong ca làm';
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
