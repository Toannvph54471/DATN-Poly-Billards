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
        'hourly_rate',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'qr_token',
        'qr_token_expires_at'
    ];

    protected $casts = [
        'salary_rate' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'qr_token_expires_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class, 'employee_id');
    }

    public function shifts()
    {
        return $this->hasMany(EmployeeShift::class, 'employee_id');
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

    // Removed mapped attributes to use actual DB columns


   


    // QR Code Methods
    public function generateQrToken()
    {
        $this->update([
            'qr_token' => \Illuminate\Support\Str::random(60),
            'qr_token_expires_at' => now()->addMinutes(2)
        ]);
        
        return $this->qr_token;
    }

    public function invalidateQrToken()
    {
        $this->update([
            'qr_token' => null,
            'qr_token_expires_at' => null
        ]);
    }
}