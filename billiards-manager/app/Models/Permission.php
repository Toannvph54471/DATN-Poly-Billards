<?php

namespace App\Models;

class Permission extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'module',
        'description',
        'created_by',
        'updated_by'
    ];

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    // Scopes
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }
}