<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $filterType = $request->get('filter', 'today');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            [$start, $end, $filterLabel] = $this->resolveDateRange($filterType, $startDate, $endDate);

            $basicData   = $this->getBasicData($start, $end);
            $chartData   = $this->getChartData($start, $end);
            $extras      = $this->getCachedExtras($start, $end);
            $growthData  = $this->calculateGrowthData($basicData, $start, $end);

            $data = array_merge([
                'filterType'         => $filterType,
                'filterLabel'         => $filterLabel,
                'startDateFormatted'  => $start->format('d/m/Y'),
                'endDateFormatted'    => $end->format('d/m/Y'),
                'start'               => $start,
                'end'                 => $end,
            ], $basicData, $chartData, $extras, $growthData);

            return view('admin.dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('admin.dashboard', $this->getFallbackData());
        }
    }

    // Dữ liệu chính cho dashboard
    private function getBasicData(Carbon $start, Carbon $end)
    {
        $revenuePaid     = $this->getRevenuePaidInRange($start, $end);
        $revenueExpected = $this->getRevenueExpectedInRange($start, $end);
        $totalRevenue    = $revenuePaid + $revenueExpected;

        $billsPaid       = $this->getBillsPaidInRange($start, $end);
        $billsExpected   = $this->getBillsExpectedInRange($start, $end);
        $totalBills      = $billsPaid + $billsExpected;

        $paidBillsDetails = $this->getPaidBillsDetails($start, $end, 10);

        return compact(
            'revenuePaid',
            'revenueExpected',
            'totalRevenue',
            'billsPaid',
            'billsExpected',
            'totalBills',
            'paidBillsDetails'
        );
    }

    private function getChartData(Carbon $start, Carbon $end)
    {
        $dailyData = $this->getDailyRevenueData($start, $end);

        $labels = $dailyData->pluck('day')->toArray();
        $paid   = $dailyData->pluck('paid_revenue')->toArray();
        $expected = $dailyData->pluck('expected_revenue')->toArray();
        $all    = array_map(fn($a, $b) => $a + $b, $paid, $expected);

        $stats = [
            'paid_max'     => max($paid ?: [0]),
            'paid_avg'     => $paid ? round(array_sum($paid) / count($paid), 0) : 0,
            'paid_total'   => array_sum($paid),
            'expected_max' => max($expected ?: [0]),
            'expected_avg' => $expected ? round(array_sum($expected) / count($expected), 0) : 0,
            'expected_total' => array_sum($expected),
            'all_max'      => max($all ?: [0]),
            'all_avg'      => $all ? round(array_sum($all) / count($all), 0) : 0,
            'all_total'    => array_sum($all),
        ];

        return [
            'chartLabels' => $labels,
            'chartData'   => compact('paid', 'expected', 'all'),
            'chartStats'  => $stats,
        ];
    }

    private function getDailyRevenueData(Carbon $start, Carbon $end)
    {
        $daysDiff = $start->diffInDays($end);
        $current  = $start->copy();
        $data     = collect();

        while ($current->lte($end)) {
            $dayStart = $current->copy()->startOfDay();
            $dayEnd   = $current->copy()->endOfDay();

            $paid     = $this->getRevenuePaidInRange($dayStart, $dayEnd);
            $expected = $this->getRevenueExpectedInRange($dayStart, $dayEnd);

            $label = $daysDiff <= 7
                ? $this->getVietnameseDayName($current->dayOfWeek)
                : ($daysDiff <= 31 ? $current->format('d/m') : 'Tuần ' . $current->weekOfYear);

            $data->push([
                'day'             => $label,
                'paid_revenue'    => $paid,
                'expected_revenue' => $expected,
                'full_date'       => $current->format('d/m/Y'),
            ]);

            $current->addDay();
        }

        return $daysDiff > 31 ? collect($this->groupByWeek($data->toArray())) : $data;
    }

    private function groupByWeek(array $items)
    {
        $grouped = [];
        foreach ($items as $item) {
            $date = Carbon::createFromFormat('d/m/Y', $item['full_date']);
            $key  = $date->year . '-W' . $date->weekOfYear;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'day'             => 'Tuần ' . $date->weekOfYear,
                    'paid_revenue'    => 0,
                    'expected_revenue' => 0,
                    'full_date'       => 'Tuần ' . $date->weekOfYear,
                ];
            }
            $grouped[$key]['paid_revenue']     += $item['paid_revenue'];
            $grouped[$key]['expected_revenue'] += $item['expected_revenue'];
        }
        return array_values($grouped);
    }

    // ===================================================================
    // DỮ LIỆU BỔ SUNG (CACHE 5 PHÚT)
    // ===================================================================
    private function getCachedExtras(Carbon $start, Carbon $end)
    {
        $key = 'dashboard_extras_' . md5($start->format('Ymd') . $end->format('Ymd'));

        return Cache::remember($key, now()->addMinutes(5), function () {
            $today      = Carbon::today();
            $monthStart = Carbon::now()->startOfMonth();
            $monthEnd   = Carbon::now()->endOfMonth();

            return [
                'topProducts'          => $this->getTopProducts(5),
                'recentBills'          => $this->getRecentBills(10),
                'tableStats'           => $this->getTableStats(),
                'lowStockProducts'     => $this->getLowStockProducts(5),
                'monthlyStats'         => $this->getMonthlyStats($monthStart, $monthEnd),
                'shiftStats'           => $this->getShiftStats($today),
                'todayRevenue'         => $this->getRevenuePaidInRange($today->startOfDay(), $today->endOfDay()),
                'todayBills'           => $this->getBillsPaidInRange($today->startOfDay(), $today->endOfDay()),
                'newCustomersToday'    => $this->getNewCustomersToday(),
                'newCustomersThisMonth' => $this->getNewCustomersThisMonth(),
                'topEmployees'         => $this->getTopPerformingEmployees(5), // ĐÃ SỬA Ở ĐÂY
            ];
        });
    }

    private function getTopPerformingEmployees($limit = 5)
    {
        return DB::table('employees as e')
            ->leftJoin('bills as b', 'e.user_id', '=', 'b.staff_id') // ĐÚNG CỘT: staff_id
            ->join('users as u', 'e.user_id', '=', 'u.id')           // để lấy tên nhân viên
            ->select(
                'e.id',
                'u.name as name',
                DB::raw('COALESCE(SUM(b.final_amount), 0) as total_revenue'),
                DB::raw('COUNT(b.id) as bill_count')
            )
            ->where('b.payment_status', 'Paid')
            ->where('e.status', 'Active')
            ->whereNull('e.deleted_at')
            ->groupBy('e.id', 'u.name')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();
    }

    // ===================================================================
    // CÁC HÀM HỖ TRỢ
    // ===================================================================
    private function calculateGrowthData(array $current, Carbon $start, Carbon $end)
    {
        $prev = $this->getPreviousPeriod($start, $end);

        $prevPaid     = $this->getRevenuePaidInRange($prev['start'], $prev['end']);
        $prevExpected = $this->getRevenueExpectedInRange($prev['start'], $prev['end']);
        $prevTotal    = $prevPaid + $prevExpected;

        $prevBillsPaid     = $this->getBillsPaidInRange($prev['start'], $prev['end']);
        $prevBillsExpected = $this->getBillsExpectedInRange($prev['start'], $prev['end']);
        $prevTotalBills    = $prevBillsPaid + $prevBillsExpected;

        return [
            'revenuePaidGrowth'     => $this->calcGrowth($current['revenuePaid'], $prevPaid),
            'revenueExpectedGrowth' => $this->calcGrowth($current['revenueExpected'], $prevExpected),
            'totalRevenueGrowth'    => $this->calcGrowth($current['totalRevenue'], $prevTotal),
            'billsPaidGrowth'       => $this->calcGrowth($current['billsPaid'], $prevBillsPaid),
            'billsExpectedGrowth'   => $this->calcGrowth($current['billsExpected'], $prevBillsExpected),
            'totalBillsGrowth'      => $this->calcGrowth($current['totalBills'], $prevTotalBills),
        ];
    }

    private function calcGrowth($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 2);
    }

    private function getPreviousPeriod(Carbon $start, Carbon $end)
    {
        $days = $start->diffInDays($end);
        return [
            'start' => $start->clone()->subDays($days + 1),
            'end'   => $end->clone()->subDays($days + 1),
        ];
    }

    private function getVietnameseDayName($dayOfWeek)
    {
        $days = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        return $days[$dayOfWeek] ?? 'Ngày';
    }

    // ===================================================================
    // QUERY DOANH THU & ĐƠN HÀNG
    // ===================================================================
    private function getRevenuePaidInRange(Carbon $start, Carbon $end)
    {
        return (int) DB::table('bills')
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'Paid')
            ->sum('final_amount');
    }

    private function getRevenueExpectedInRange(Carbon $start, Carbon $end)
    {
        return (int) DB::table('bills')
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', '!=', 'Paid')
            ->whereIn('status', ['Open', 'Pending'])
            ->sum('final_amount');
    }

    private function getBillsPaidInRange(Carbon $start, Carbon $end)
    {
        return DB::table('bills')
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'Paid')
            ->count();
    }

    private function getBillsExpectedInRange(Carbon $start, Carbon $end)
    {
        return DB::table('bills')
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', '!=', 'Paid')
            ->whereIn('status', ['Open', 'Pending'])
            ->count();
    }

    private function getPaidBillsDetails(Carbon $start, Carbon $end, $limit = 10)
    {
        return DB::table('bills')
            ->select('bills.*', 'tables.table_name', 'users.name as customer_name')
            ->leftJoin('tables', 'bills.table_id', '=', 'tables.id')
            ->leftJoin('users', 'bills.user_id', '=', 'users.id')
            ->whereBetween('bills.created_at', [$start, $end])
            ->where('bills.payment_status', 'Paid')
            ->orderByDesc('bills.created_at')
            ->limit($limit)
            ->get()
            ->map(fn($b) => [
                'id'            => $b->id,
                'bill_number'   => $b->bill_number,
                'table_name'    => $b->table_name ?? 'Bàn ' . $b->table_id,
                'customer_name' => $b->customer_name ?? 'Khách vãng lai',
                'total_amount'  => $b->final_amount,
                'status'        => $b->status,
                'payment_status' => $b->payment_status,
                'created_at'    => $b->created_at,
                'time_ago'      => Carbon::parse($b->created_at)->diffForHumans(),
                'formatted_date' => Carbon::parse($b->created_at)->format('H:i d/m/Y'),
            ]);
    }

    // ===================================================================
    // CÁC HÀM KHÁC (giữ nguyên logic cũ, chỉ tối ưu nhẹ)
    // ===================================================================
    private function getTopProducts($limit = 5)
    {
        return DB::table('bill_details')
            ->join('products', 'bill_details.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.name',
                DB::raw('COALESCE(categories.name, "Chưa phân loại") as category_name'),
                DB::raw('SUM(bill_details.quantity) as total_quantity'),
                DB::raw('SUM(bill_details.total_price) as total_revenue')
            )
            ->whereNotNull('bill_details.product_id')
            ->groupBy('products.id', 'products.name', 'categories.name')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }

    private function getRecentBills($limit = 10)
    {
        return DB::table('bills')
            ->select('bills.*', 'tables.table_name', 'users.name as customer_name')
            ->leftJoin('tables', 'bills.table_id', '=', 'tables.id')
            ->leftJoin('users', 'bills.user_id', '=', 'users.id')
            ->orderByDesc('bills.created_at')
            ->limit($limit)
            ->get()
            ->map(fn($b) => [
                'id'            => $b->id,
                'bill_number'   => $b->bill_number,
                'table_name'    => $b->table_name ?? 'Bàn ' . $b->table_id,
                'customer_name' => $b->customer_name ?? 'Khách vãng lai',
                'total_amount'  => $b->final_amount,
                'status'        => $b->status,
                'payment_status' => $b->payment_status,
                'created_at'    => $b->created_at,
                'time_ago'      => Carbon::parse($b->created_at)->diffForHumans(),
            ]);
    }

    private function getLowStockProducts($limit = 5)
    {
        return DB::table('products')
            ->whereRaw('stock_quantity <= min_stock_level')
            ->where('status', 'Active')
            ->whereNull('deleted_at')
            ->orderBy('stock_quantity')
            ->limit($limit)
            ->get();
    }

    private function getTableStats()
    {
        $stats = DB::table('tables')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "occupied" THEN 1 ELSE 0 END) as occupied,
                SUM(CASE WHEN status = "reserved" THEN 1 ELSE 0 END) as reserved,
                SUM(CASE WHEN status = "maintenance" THEN 1 ELSE 0 END) as maintenance,
                SUM(CASE WHEN status = "available" THEN 1 ELSE 0 END) as available
            ')
            ->whereNull('deleted_at')
            ->first();

        $total = $stats->total ?? 0;
        $used  = ($stats->occupied ?? 0) + ($stats->reserved ?? 0);

        return [
            'total'          => $total,
            'occupied'       => $stats->occupied ?? 0,
            'reserved'       => $stats->reserved ?? 0,
            'available'      => $stats->available ?? 0,
            'maintenance'    => $stats->maintenance ?? 0,
            'occupancy_rate' => $total > 0 ? round(($used / $total) * 100, 2) : 0,
        ];
    }

    private function getShiftStats(Carbon $date)
    {
        $active = DB::table('employee_shifts')
            ->whereDate('shift_date', $date)
            ->where('status', 'active')
            ->count();

        $hours = DB::table('employee_shifts')
            ->whereDate('shift_date', $date)
            ->where('status', 'completed')
            ->sum('total_hours') ?? 0;

        $working = DB::table('employee_shifts')
            ->join('employees', 'employee_shifts.employee_id', '=', 'employees.id')
            ->whereDate('employee_shifts.shift_date', $date)
            ->where('employee_shifts.status', 'active')
            ->select('employees.name', 'employee_shifts.actual_start_time')
            ->limit(5)
            ->get();

        return compact('active', 'hours', 'working');
    }

    private function getMonthlyStats(Carbon $start, Carbon $end)
    {
        $paid     = $this->getRevenuePaidInRange($start, $end);
        $expected = $this->getRevenueExpectedInRange($start, $end);
        $total    = $paid + $expected;
        $bills    = DB::table('bills')->whereBetween('created_at', [$start, $end])->count();
        $customers = $this->getNewCustomersInRange($start, $end);

        return compact('total', 'bills', 'customers', 'paid', 'expected');
    }

    private function getNewCustomersInRange(Carbon $start, Carbon $end)
    {
        return DB::table('users')
            ->where('role_id', 4)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    private function getNewCustomersToday()
    {
        $today = Carbon::today();
        return $this->getNewCustomersInRange($today->startOfDay(), $today->endOfDay());
    }

    private function getNewCustomersThisMonth()
    {
        return $this->getNewCustomersInRange(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
    }

    private function resolveDateRange($type, $startDate = null, $endDate = null)
    {
        $now = Carbon::now();

        return match ($type) {
            'yesterday' => [$now->clone()->subDay()->startOfDay(), $now->clone()->subDay()->endOfDay(), 'Hôm qua (' . $now->subDay()->format('d/m/Y') . ')'],
            'week'      => [$now->clone()->startOfWeek(), $now->clone()->endOfWeek(), 'Tuần này'],
            'last_week' => [$now->clone()->subWeek()->startOfWeek(), $now->clone()->subWeek()->endOfWeek(), 'Tuần trước'],
            'month'     => [$now->clone()->startOfMonth(), $now->clone()->endOfMonth(), 'Tháng này'],
            'last_month' => [$now->clone()->subMonthNoOverflow()->startOfMonth(), $now->clone()->subMonthNoOverflow()->endOfMonth(), 'Tháng trước'],
            'custom'    => [
                $startDate ? Carbon::parse($startDate)->startOfDay() : $now->startOfDay(),
                $endDate   ? Carbon::parse($endDate)->endOfDay()     : $now->endOfDay(),
                'Từ ' . Carbon::parse($startDate ?? now())->format('d/m') . ' → ' . Carbon::parse($endDate ?? now())->format('d/m/Y')
            ],
            default     => [$now->clone()->startOfDay(), $now->clone()->endOfDay(), 'Hôm nay (' . $now->format('d/m/Y') . ')'],
        };
    }

    private function getFallbackData()
    {
        return [
            'revenuePaid' => 0,
            'revenueExpected' => 0,
            'totalRevenue' => 0,
            'billsPaid' => 0,
            'billsExpected' => 0,
            'totalBills' => 0,
            'chartLabels' => [],
            'chartData' => ['paid' => [], 'expected' => [], 'all' => []],
            'chartStats' => array_fill_keys(['paid_max', 'paid_avg', 'paid_total', 'expected_max', 'expected_avg', 'expected_total', 'all_max', 'all_avg', 'all_total'], 0),
            'paidBillsDetails' => collect(),
            'topProducts' => collect(),
            'recentBills' => collect(),
            'lowStockProducts' => collect(),
            'tableStats' => ['total' => 0, 'occupied' => 0, 'reserved' => 0, 'available' => 0, 'maintenance' => 0, 'occupancy_rate' => 0],
            'monthlyStats' => ['total' => 0, 'bills' => 0, 'customers' => 0, 'paid' => 0, 'expected' => 0],
            'shiftStats' => ['active' => 0, 'hours' => 0, 'working' => collect()],
            'todayRevenue' => 0,
            'todayBills' => 0,
            'newCustomersToday' => 0,
            'newCustomersThisMonth' => 0,
            'topEmployees' => collect(),
            'filterLabel' => 'Hôm nay',
            'filterType' => 'today',
            'startDateFormatted' => now()->format('d/m/Y'),
            'endDateFormatted'   => now()->format('d/m/Y'),
            // growth
            'revenuePaidGrowth' => 0,
            'revenueExpectedGrowth' => 0,
            'totalRevenueGrowth' => 0,
            'billsPaidGrowth' => 0,
            'billsExpectedGrowth' => 0,
            'totalBillsGrowth' => 0,
        ];
    }

    public function refreshData(Request $request)
    {
        try {
            $filterType = $request->get('filter', 'today');
            $startDate  = $request->input('start_date');
            $endDate    = $request->input('end_date');

            [$start, $end] = $this->resolveDateRange($filterType, $startDate, $endDate);
            $key = 'dashboard_extras_' . md5($start->format('Ymd') . $end->format('Ymd'));
            Cache::forget($key);

            return response()->json(['success' => true, 'message' => 'Đã làm mới dữ liệu']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi khi làm mới'], 500);
        }
    }
}
