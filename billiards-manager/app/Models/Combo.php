<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'actual_value',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'actual_value' => 'decimal:2'
    ];

    // Relationships
    public function billDetails()
    {
        return $this->hasMany(BillDetail::class);
    }

    public function items()
    {
        return $this->hasMany(ComboItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'combo_items')
            ->withPivot('quantity', 'is_required', 'choice_group', 'max_choices')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    // Methods
    public function getSavingsAttribute()
    {
        return $this->actual_value - $this->price;
    }

    public function getSavingsPercentageAttribute()
    {
        if ($this->actual_value > 0) {
            return ($this->savings / $this->actual_value) * 100;
        }
        return 0;
    }

    public function getRequiredProducts()
    {
        return $this->items()->where('is_required', true)->get();
    }

    public function getChoiceGroups()
    {
        return $this->items()
            ->where('is_required', false)
            ->whereNotNull('choice_group')
            ->select('choice_group', 'max_choices')
            ->distinct()
            ->get();
    }
}