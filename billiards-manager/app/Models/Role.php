<?php

namespace App\Models;

class Role extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'permissions' => 'array'
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Methods
    public function hasPermission($permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function addPermission($permission): bool
    {   
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            return $this->update(['permissions' => $permissions]);
        }
        return true;
    }
}