<?php

namespace App\Models;

class Attendance extends BaseModel
{
    protected $table = 'attendance';

    protected $fillable = [
        'employee_id',
        'check_in',
        'check_out',
        'status',
        'late_minutes',
        'early_minutes',
        'late_reason',
        'approval_status',
        'approved_by',
        'approved_at',
        'total_minutes',
        'confirmed_by',
        'note',
        'latitude',
        'longitude',
        'created_by',
        'admin_checkout_by',
        'admin_checkout_reason'
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'approved_at' => 'datetime',
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

    public function confirmedByUser()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function adminCheckoutUser()
    {
        return $this->belongsTo(User::class, 'admin_checkout_by');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('check_in', today());
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}
