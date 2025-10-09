<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'shift_id',
        'shift_date',
        'actual_start_time',
        'actual_end_time', 
        'status',
        'confirmed_by',
        'note'
    ];

    protected $casts = [
        'shift_date' => 'date',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('shift_date', today());
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'Scheduled');
    }

    public function scopeWorking($query)
    {
        return $query->where('status', 'Working');
    }

    // Methods
    public function getActualDuration()
    {
        if ($this->actual_start_time && $this->actual_end_time) {
            return $this->actual_end_time->diffInMinutes($this->actual_start_time) / 60;
        }
        return 0;
    }
}