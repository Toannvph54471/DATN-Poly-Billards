<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class TableRate extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'code',
        'name',
        'hourly_rate',
        'max_hours',
        'status'
    ];

    public function tables()
    {
        return $this->hasMany(Table::class);
    }
}
