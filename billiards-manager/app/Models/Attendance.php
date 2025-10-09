<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'check_in',
        'check_out',
        'status',
        'confirmed_by',
        'note'
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('check_in', today());
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'Present');
    }

    // Methods
    public function getWorkHours()
    {
        if ($this->check_out) {
            return $this->check_out->diffInMinutes($this->check_in) / 60;
        }
        return 0;
    }

    public function isLate()
    {
        $employeeShift = $this->employee->shifts()
            ->whereDate('shift_date', $this->check_in->toDateString())
            ->first();

        if ($employeeShift && $employeeShift->shift) {
            return $this->check_in->format('H:i') > $employeeShift->shift->start_time->format('H:i');
        }

        return false;
    }
}