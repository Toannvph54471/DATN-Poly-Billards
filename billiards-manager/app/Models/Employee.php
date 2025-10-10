<?php

namespace App\Models;

class Employee extends BaseModel
{
    const POSITION_MANAGER = 'manager';
    const POSITION_STAFF = 'staff';
    const POSITION_CASHIER = 'cashier';
    const POSITION_WAITER = 'waiter';

    protected $fillable = [
        'employee_code',
        'user_id',
        'name',
        'phone',
        'email',
        'address',
        'position',
        'salary_type',
        'base_salary',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    // Methods
    public function isWorking(): bool
    {
        return $this->is_active && 
               (!$this->end_date || $this->end_date >= now());
    }

    public function getCurrentShift()
    {
        return $this->employeeShifts()
                    ->whereDate('shift_date', today())
                    ->where('status', EmployeeShift::STATUS_ACTIVE)
                    ->first();
    }
}