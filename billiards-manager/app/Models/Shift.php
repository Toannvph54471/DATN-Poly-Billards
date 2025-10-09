<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_time', 'end_time'];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i'
    ];

    // Relationships
    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class);
    }

    // Methods
    public function getDurationAttribute()
    {
        return $this->start_time->diffInHours($this->end_time);
    }
}