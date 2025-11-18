<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'type', // 'product' or 'table'
        'rate_code', // Mã bảng giá
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

    /**
     * Quan hệ với TableRate
     */
    public function tableRate()
    {
        return $this->belongsTo(TableRate::class, 'rate_code', 'code');
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

    public function getHourlyRateAttribute()
    {
        return $this->tableRate ? $this->tableRate->hourly_rate : 0;
    }
}
