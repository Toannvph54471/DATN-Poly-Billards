<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'period',
        'total_hours',
        'base_salary',
        'overtime_hours',
        'overtime_pay',
        'bonus',
        'penalty',
        'total_amount',
        'status',
        'paid_at'
    ];

    protected $casts = [
        'total_hours' => 'decimal:2',
        'base_salary' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonus' => 'decimal:2',
        'penalty' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'Paid');
    }

    public function scopePeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    // Methods
    public function calculateTotalAmount()
    {
        $this->total_amount = $this->base_salary + $this->overtime_pay + $this->bonus - $this->penalty;
        return $this->total_amount;
    }
}