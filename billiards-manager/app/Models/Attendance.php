<?php

namespace App\Models;

class Attendance extends BaseModel
{
    const TYPE_CHECK_IN = 'check_in';
    const TYPE_CHECK_OUT = 'check_out';
    const TYPE_BREAK_START = 'break_start';
    const TYPE_BREAK_END = 'break_end';

    protected $fillable = [
        'employee_id',
        'employee_shift_id',
        'type',
        'time',
        'notes',
        'latitude',
        'longitude',
        'created_by'
    ];

    protected $casts = [
        'time' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function employeeShift()
    {
        return $this->belongsTo(EmployeeShift::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('time', today());
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    // Methods
    public function isCheckIn(): bool
    {
        return $this->type === self::TYPE_CHECK_IN;
    }

    public function isCheckOut(): bool
    {
        return $this->type === self::TYPE_CHECK_OUT;
    }
}