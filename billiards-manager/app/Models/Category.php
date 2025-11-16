<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'type', // 'product' | 'table'
        'description',
        'status',
    ];

    // Relationships
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    // Scopes
    public function scopeTable($query)
    {
        return $query->where('type', 'table');
    }

    public function scopeProduct($query)
    {
        return $query->where('type', 'product');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getAvailableTablesCount(): int
    {
        return $this->tables()
            ->where('status', 'available')
            ->count();
    }
}
