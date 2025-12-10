<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_EMPLOYEE = 'employee';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role_id',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    public function shifts()
    {
        return $this->hasMany(EmployeeShift::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function isAdmin(): bool
    {
        return $this->role && $this->role->slug === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role && $this->role->slug === self::ROLE_MANAGER;
    }

    public function isEmployee(): bool
    {
        return $this->role && $this->role->slug === self::ROLE_EMPLOYEE;
    }

    public function sendPasswordResetNotification($token)
    {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $this->email
        ], false));

        $this->notify(new ResetPasswordNotification($url));
    }

    public function addedBillDetails()
    {
        return $this->hasMany(BillDetail::class, 'added_by');
    }

    // Quan hệ với các bill đã tạo (nếu có cột staff_id trong bills)
    public function createdBills()
    {
        return $this->hasMany(Bill::class, 'staff_id');
    }
}
