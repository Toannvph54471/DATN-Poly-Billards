<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DailyReport extends Model
{
    protected $table = 'daily_reports';

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

    // Scope để lọc theo khoảng thời gian
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('report_date', [$startDate, $endDate]);
    }

    // Scope để sắp xếp theo ngày
    public function scopeOrderByDate($query, $direction = 'desc')
    {
        return $query->orderBy('report_date', $direction);
    }

    // Tính tổng doanh thu theo khoảng thời gian
    public static function getTotalRevenue($startDate = null, $endDate = null)
    {
        $query = self::query();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return $query->sum('total_revenue');
    }

    // Lấy báo cáo hàng ngày với phân trang
    public static function getDailyReports($perPage = 15, $startDate = null, $endDate = null)
    {
        $query = self::query()->orderByDate();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return $query->paginate($perPage);
    }

    // Tạo báo cáo cho một ngày cụ thể
    public static function generateReportForDate($date)
    {
        // Tính toán từ bảng bills và users
        $reportData = DB::table('bills as b')
            ->selectRaw("
                DATE('{$date}') as report_date,
                COALESCE(SUM(b.final_amount), 0) as total_revenue,
                COALESCE(SUM(b.discount_amount), 0) as total_discount,
                COUNT(b.id) as total_bills,
                COUNT(DISTINCT CASE WHEN b.user_id IS NOT NULL THEN b.user_id END) as total_customers,
                COALESCE(AVG(b.final_amount), 0) as average_bill_value
            ")
            ->where('b.status', 'Closed')
            ->where('b.payment_status', 'Paid')
            ->whereDate('b.created_at', $date)
            ->first();

        // Lưu hoặc cập nhật báo cáo
        return self::updateOrCreate(
            ['report_date' => $date],
            [
                'total_revenue' => $reportData->total_revenue,
                'total_discount' => $reportData->total_discount,
                'total_bills' => $reportData->total_bills,
                'total_customers' => $reportData->total_customers,
                'average_bill_value' => $reportData->average_bill_value
            ]
        );
    }

    // Tạo báo cáo cho nhiều ngày
    public static function generateReportsForDateRange($startDate, $endDate)
    {
        $dates = [];
        $currentDate = $startDate;

        while ($currentDate <= $endDate) {
            self::generateReportForDate($currentDate);
            $dates[] = $currentDate;
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        return $dates;
    }
}
