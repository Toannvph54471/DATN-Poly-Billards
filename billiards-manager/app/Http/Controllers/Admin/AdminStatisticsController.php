<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminStatisticsController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'week');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        [$start, $end, $label] = $this->resolveDateRange($filter, $startDate, $endDate);

        return view('admin.statistics.index', [
            'filter'         => $filter,
            'label'          => $label,
            'start'          => $start,
            'end'            => $end,

            // Tổng quan
            'totalRevenue'   => $this->sumTotalRevenue($start, $end),
            'totalBills'     => $this->countBills($start, $end),
            'totalCustomers' => $this->countCustomers($start, $end),
            'growth'         => $this->calcGrowthPeriod($start, $end),
            
            // Attendance custom stats
            'lateCount'      => $this->countLate($start, $end),
            'missedCheckoutCount' => $this->countMissedCheckout($start, $end),
            'totalHoursMonth' => $this->countTotalHours($start, $end),

            // Chi tiết
            'revenueByDay'   => $this->statsRevenueByDay($start, $end),
            'perTable'       => $this->statsPerTable($start, $end),
            'serviceStats'   => $this->statsServices($start, $end),
            'employeeStats'  => $this->statsEmployees($start, $end),
            'peak'           => $this->peakHours($start, $end),
        ]);
    }

    /* =====================================================
       BASIC
    ===================================================== */

    private function sumTotalRevenue($start, $end)
    {
        return DB::table('bills')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');
    }

    private function countBills($start, $end)
    {
        return DB::table('bills')
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    private function countCustomers($start, $end)
    {
        if (!Schema::hasColumn('users', 'role_id')) {
            return 0;
        }

        return DB::table('users')
            ->where('role_id', 4)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    /* =====================================================
       1. DOANH THU THEO NGÀY
    ===================================================== */

    private function statsRevenueByDay($start, $end)
    {
        return DB::table('bills as b')
            ->leftJoinSub(
                DB::table('bill_details')
                    ->selectRaw('bill_id, SUM(total_price) as service_total')
                    ->groupBy('bill_id'),
                'bd',
                'bd.bill_id',
                '=',
                'b.id'
            )
            ->whereBetween('b.created_at', [$start, $end])
            ->selectRaw('
                DATE(b.created_at) as date,
                SUM(b.total_amount) as total_revenue,
                SUM(IFNULL(bd.service_total,0)) as service_revenue,
                (SUM(b.total_amount) - SUM(IFNULL(bd.service_total,0))) as play_revenue,
                COUNT(DISTINCT b.table_id) as active_tables,
                COUNT(b.id) as sessions
            ')
            ->groupBy(DB::raw('DATE(b.created_at)'))
            ->orderBy('date')
            ->get();
    }

    /* =====================================================
       2. THỐNG KÊ THEO BÀN
    ===================================================== */

    private function statsPerTable($start, $end)
    {
        return DB::table('tables as t')
            ->leftJoin('bills as b', function ($join) use ($start, $end) {
                $join->on('b.table_id', '=', 't.id')
                     ->whereBetween('b.created_at', [$start, $end]);
            })
            ->whereNull('t.deleted_at')
            ->selectRaw('
                t.id,
                t.table_name,
                COUNT(b.id) as total_sessions,
                COALESCE(SUM(b.total_amount),0) as revenue,
                0 as total_hours
            ')
            ->groupBy('t.id', 't.table_name')
            ->orderByDesc('revenue')
            ->get();
    }

    /* =====================================================
       3. DỊCH VỤ
    ===================================================== */

    private function statsServices($start, $end)
    {
        $top = DB::table('bill_details as bd')
            ->join('bills as b', 'bd.bill_id', '=', 'b.id')
            ->join('products as p', 'bd.product_id', '=', 'p.id')
            ->whereBetween('b.created_at', [$start, $end])
            ->selectRaw('
                p.name,
                SUM(bd.quantity) as qty,
                SUM(bd.total_price) as revenue
            ')
            ->groupBy('p.name')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        $profit = DB::table('bill_details as bd')
            ->join('bills as b', 'bd.bill_id', '=', 'b.id')
            ->whereBetween('b.created_at', [$start, $end])
            ->selectRaw('
                SUM(bd.total_price) as revenue
            ')
            ->first();

        return [
            'top'    => $top,
            'profit' => $profit,
        ];
    }

    /* =====================================================
       4. NHÂN VIÊN
    ===================================================== */

    private function statsEmployees($start, $end)
    {
        return DB::table('employees as e')
            ->leftJoin('bills as b', function ($join) use ($start, $end) {
                $join->on('b.staff_id', '=', 'e.id')
                     ->whereBetween('b.created_at', [$start, $end]);
            })
            ->selectRaw('
                e.id,
                e.name as employee_name,
                COUNT(b.id) as bills_count,
                COALESCE(SUM(b.total_amount),0) as total_collected,
                0 as session_hours
            ')
            ->groupBy('e.id', 'e.name')
            ->orderByDesc('total_collected')
            ->get();
    }

    /* =====================================================
       5. GIỜ CAO ĐIỂM
    ===================================================== */

    private function peakHours($start, $end)
    {
        $byHour = DB::table('bills')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('
                HOUR(created_at) as hour,
                COUNT(*) as cnt,
                SUM(total_amount) as revenue
            ')
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderByDesc('revenue')
            ->get();

        $ranges = [
            'Sáng (6–11h)'   => [6, 11],
            'Trưa (11–14h)'  => [11, 14],
            'Chiều (14–18h)' => [14, 18],
            'Tối (18–23h)'   => [18, 23],
        ];

        $rangeStats = [];
        foreach ($ranges as $label => [$from, $to]) {
            $rangeStats[$label] = DB::table('bills')
                ->whereBetween('created_at', [$start, $end])
                ->whereBetween(DB::raw('HOUR(created_at)'), [$from, $to])
                ->selectRaw('
                    COUNT(*) as cnt,
                    SUM(total_amount) as revenue
                ')
                ->first();
        }

        return [
            'byHour'     => $byHour,
            'rangeStats' => $rangeStats,
        ];
    }

    /* =====================================================
       UTILITIES
    ===================================================== */

    private function resolveDateRange($type, $startDate, $endDate)
    {
        return match ($type) {
            'week' => [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
                'Tuần này'
            ],
            'month' => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
                'Tháng này'
            ],
            'custom' => [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
                'Tùy chọn'
            ],
            default => [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay(),
                'Hôm nay'
            ],
        };
    }

    private function calcGrowthPeriod($start, $end)
    {
        $days = $start->diffInDays($end) + 1;

        $prevStart = $start->copy()->subDays($days);
        $prevEnd   = $end->copy()->subDays($days);

        $current = DB::table('bills')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');

        $previous = DB::table('bills')
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->sum('total_amount');

        return $previous > 0
            ? round((($current - $previous) / $previous) * 100, 1)
            : 0;
    }

    /* =====================================================
       ATTENDANCE STATS
    ===================================================== */
    private function countLate($start, $end)
    {
         if (!Schema::hasTable('attendance')) return 0;
         
         return DB::table('attendance')
            ->whereBetween('check_in', [$start, $end])
            ->where(function($q) {
                $q->where('status', 'like', '%Late%')
                  ->orWhere('late_minutes', '>', 0);
            })
            ->count();
    }

    private function countMissedCheckout($start, $end)
    {
        if (!Schema::hasTable('attendance')) return 0;
        
        // Logic: Has check_in but check_out is null and day has passed (or shift end passed)
        // Simply checking check_out is null for past dates in range
        return DB::table('attendance')
            ->whereBetween('check_in', [$start, $end])
            ->whereNull('check_out')
            ->where('check_in', '<', now()->subHours(12)) // Assume shift not longer than 12h
            ->count();
    }

    private function countTotalHours($start, $end)
    {
        if (!Schema::hasTable('attendance')) return 0;
        
        $minutes = DB::table('attendance')
            ->whereBetween('check_in', [$start, $end])
            ->sum('total_minutes');
            
        return round($minutes / 60, 1);
    }
}
