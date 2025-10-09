<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'position', 
        'salary_rate',
        'hire_date',
        'status'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary_rate' => 'decimal:2'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shifts()
    {
        return $this->hasMany(EmployeeShift::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payroll()
    {
        return $this->hasMany(Payroll::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    // Methods
    public function getCurrentShift()
    {
        return $this->shifts()
            ->whereDate('shift_date', today())
            ->where('status', 'Working')
            ->first();
    }

    public function getMonthlyHours($year, $month)
    {
        return $this->attendance()
            ->whereYear('check_in', $year)
            ->whereMonth('check_in', $month)
            ->whereNotNull('check_out')
            ->get()
            ->sum(function($attendance) {
                return $attendance->check_out->diffInHours($attendance->check_in);
            });
    }
}