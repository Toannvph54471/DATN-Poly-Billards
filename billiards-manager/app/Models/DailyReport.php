<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date',
        'total_revenue',
        'total_discount',
        'total_bills',
        'total_customers',
        'average_bill_value'
    ];

    protected $casts = [
        'report_date' => 'date',
        'total_revenue' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'average_bill_value' => 'decimal:2'
    ];

    // Scopes
    public function scopeToday($query)
    {
        return $query->where('report_date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('report_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('report_date', now()->year)
            ->whereMonth('report_date', now()->month);
    }

    // Methods
    public static function generateForDate($date = null)
    {
        $date = $date ?: today();

        $bills = Bill::whereDate('created_at', $date)
            ->where('status', 'Closed')
            ->get();

        $totalRevenue = $bills->sum('final_amount');
        $totalDiscount = $bills->sum('discount_amount');
        $totalBills = $bills->count();
        $totalCustomers = $bills->filter(fn($bill) => $bill->customer_id)->count();
        $averageBillValue = $totalBills > 0 ? $totalRevenue / $totalBills : 0;

        return static::updateOrCreate(
            ['report_date' => $date],
            [
                'total_revenue' => $totalRevenue,
                'total_discount' => $totalDiscount,
                'total_bills' => $totalBills,
                'total_customers' => $totalCustomers,
                'average_bill_value' => $averageBillValue
            ]
        );
    }
}