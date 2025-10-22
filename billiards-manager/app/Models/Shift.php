<?php

namespace App\Models;

class Shift extends BaseModel
{
    protected $fillable = [
        'shift_code',
        'name',
        'start_time',
        'end_time',
        'description',
        'color',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    // Relationships
    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class);
    }

    // Methods
    public function getDuration(): int
    {
        return $this->start_time->diffInHours($this->end_time);
    }

    public function isCurrentShift(): bool
    {
        $now = now()->format('H:i:s');
        return $now >= $this->start_time->format('H:i:s') && 
               $now <= $this->end_time->format('H:i:s');
    }
}