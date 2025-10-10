<?php

namespace App\Models;

class Payroll extends BaseModel
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'payroll_code',
        'employee_id',
        'period_start',
        'period_end',
        'base_salary',
        'overtime_hours',
        'overtime_pay',
        'bonus',
        'deductions',
        'total_amount',
        'payment_date',
        'status',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deductions' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'payment_date' => 'date'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopePeriod($query, $start, $end)
    {
        return $query->where('period_start', $start)
                    ->where('period_end', $end);
    }

    // Methods
    public function calculateTotal(): float
    {
        return $this->base_salary + $this->overtime_pay + $this->bonus - $this->deductions;
    }

    public function markAsPaid(): bool
    {
        return $this->update([
            'status' => self::STATUS_PAID,
            'payment_date' => now()
        ]);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }
}