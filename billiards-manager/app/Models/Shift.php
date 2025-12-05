<?php

namespace App\Models;

class Shift extends BaseModel
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'status',
        'salary_multiplier',
        'wage',
        'late_allow',
        'late_penalty',
        'early_penalty'
    ];

    protected $casts = [
        // 'start_time' => 'datetime', // Removed to prevent date attachment
        // 'end_time' => 'datetime'    // Treated as string "H:i:s"
    ];

    // Relationships
    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class);
    }

    // Methods
    // Methods
    public function getDuration(): int
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        
        if ($end->lt($start)) {
            $end->addDay();
        }
        
        return $start->diffInHours($end);
    }

    public function isCurrentShift(): bool
    {
        $now = now()->format('H:i:s');
        return $now >= $this->start_time && $now <= $this->end_time;
    }
}
