<?php

namespace App\Models;

class DailyReport extends BaseModel
{
    protected $fillable = [
        'report_date',
        'total_bills',
        'total_revenue',
        'table_revenue',
        'product_revenue',
        'total_customers',
        'total_discount',
        'average_bill_value',
        'most_popular_table',
        'most_sold_product',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'report_date' => 'date',
        'total_revenue' => 'decimal:2',
        'table_revenue' => 'decimal:2',
        'product_revenue' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'average_bill_value' => 'decimal:2'
    ];

    // Scopes
    public function scopeByDate($query, $date)
    {
        return $query->where('report_date', $date);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('report_date', now()->year)
                    ->whereMonth('report_date', now()->month);
    }

    // Methods
    public function calculateAverageBill(): float
    {
        if ($this->total_bills == 0) return 0;
        return $this->total_revenue / $this->total_bills;
    }
}