<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_time', 'end_time'];

    /**
     * Format kiểu dữ liệu giờ tự động (khi lấy ra)
     * => luôn hiển thị 24h (HH:mm)
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
        ];
    }

    /**
     * Quan hệ 1-nhiều với EmployeeShift
     */
    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class);
    }

    /**
     * Quan hệ nhiều-nhiều với Employee
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_shifts')
            ->withPivot('shift_date', 'status', 'note', 'actual_start_time', 'actual_end_time')
            ->withTimestamps();
    }

    /**
     * Helper: Lấy thời gian định dạng dễ đọc (VD: 08:00 - 16:00)
     */
    public function getTimeRangeAttribute(): string
    {
        return "{$this->start_time->format('H:i')} - {$this->end_time->format('H:i')}";
    }
}
