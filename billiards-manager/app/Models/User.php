<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email', 
        'phone',
        'role_id',
        'password',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'staff_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'created_by');
    }

    public function confirmedShifts()
    {
        return $this->hasMany(EmployeeShift::class, 'confirmed_by');
    }

    public function confirmedAttendance()
    {
        return $this->hasMany(Attendance::class, 'confirmed_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    // Methods
    public function hasPermission($permissionName)
    {
        return $this->role->permissions()->where('name', $permissionName)->exists();
    }

    public function isAdmin()
    {
        return $this->role_id === 1;
    }

    public function isManager()
    {
        return $this->role_id === 2;
    }

    public function isStaff()
    {
        return $this->role_id === 3;
    }
}