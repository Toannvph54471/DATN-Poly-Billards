<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use  Notifiable, HasFactory;

    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_EMPLOYEE = 'employee';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'email_verified_at'
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

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function shifts()
    {
        return $this->hasMany(EmployeeShift::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Helper methods
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
}