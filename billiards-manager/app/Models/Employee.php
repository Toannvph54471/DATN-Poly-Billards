<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends BaseModel
{
    use HasFactory;

    const POSITION_MANAGER = 'manager';
    const POSITION_STAFF = 'staff';
    const POSITION_CASHIER = 'cashier';
    const POSITION_WAITER = 'waiter';

    protected $fillable = [
        'user_id',
        'employee_code',
        'name',
        'phone',
        'email',
        'address',
        'position',
        'salary_type',
        'salary_rate',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'salary_rate' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
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
        return $query->where('status', 1);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    // Methods
    public function isWorking(): bool
    {
        return $this->status === 1 &&
               (!$this->end_date || $this->end_date >= now());
    }

    public function getCurrentShift()
    {
        return $this->employeeShifts()
                    ->whereDate('shift_date', today())
                    ->where('status', EmployeeShift::STATUS_ACTIVE)
                    ->first();
    }

    // Setter cho salary_rate dựa trên salary_type
    public function setSalaryRateAttribute($value)
    {
        if ($this->salary_type === 'monthly' && !$value) {
            $this->attributes['salary_rate'] = 35000.00;
        } elseif ($this->salary_type === 'hourly' && !$value) {
            $this->attributes['salary_rate'] = 25000.00;
        } else {
            $this->attributes['salary_rate'] = $value;
        }
    }

   
}