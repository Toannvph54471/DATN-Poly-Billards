<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        try {
            // ================== LẤY BỘ LỌC TỪ REQUEST ==================
            $filterType = $request->get('filter', 'today'); // today, yesterday, week, month, custom
            $startDate  = $request->input('start_date');
            $endDate    = $request->input('end_date');

            // Xác định khoảng thời gian thực tế + nhãn hiển thị
            [$start, $end, $filterLabel] = $this->resolveDateRange($filterType, $startDate, $endDate);

            // Cache key duy nhất theo filter
            $cacheKey = 'dashboard_' . md5("filter_{$filterType}_{$startDate}_{$endDate}");

            $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($start, $end) {
                return [
                    // Doanh thu & bill trong khoảng thời gian lọc
                    'todayRevenue'       => $this->getRevenueInRange($start, $end),
                    'todayBills'         => $this->getBillsInRange($start, $end),
                    'newCustomersToday'  => $this->getNewCustomersInRange($start, $end),

                    // Doanh thu theo từng ngày trong khoảng (dùng cho biểu đồ)
                    'weeklyRevenue'      => $this->getRevenueByDay($start, $end),

                    // Các thống kê khác (giữ nguyên)
                    'topProducts'        => $this->getTopProducts(5),
                    'recentBills'        => $this->getRecentBills(10),
                    'activeEmployees'    => $this->getActiveEmployeesInRange($start, $end, 5),
                    'lowStockProducts'   => $this->getLowStockProducts(5),
                    'tableStats'         => $this->getTableStats(),
                    'shiftStats'         => $this->getShiftStats(Carbon::today()),
                    'monthlyStats'       => $this->getMonthlyStats($start->copy()->startOfMonth(), $start->copy()->endOfMonth()),
                ];
            });

            // Tính kỳ trước để so sánh tăng trưởng
            $previous = $this->getPreviousPeriod($start, $end);
            $yesterdayRevenue = $this->getRevenueInRange($previous['start'], $previous['end']);
            $yesterdayBills   = $this->getBillsInRange($previous['start'], $previous['end']);

            $revenueGrowth = $this->calculateRevenueGrowth($data['todayRevenue'], $yesterdayRevenue);
            $billGrowth    = $this->calculateBillGrowth($data['todayBills'], $yesterdayBills);

            // Truyền thêm thông tin filter để hiển thị trên giao diện
            return view('admin.dashboard', array_merge($data, [
                'yesterdayRevenue'     => $yesterdayRevenue,
                'yesterdayBills'       => $yesterdayBills,
                'revenueGrowth'        => $revenueGrowth,
                'billGrowth'           => $billGrowth,
                'newCustomersThisMonth' => $this->getNewCustomersThisMonth($start->copy()->startOfMonth(), $start->copy()->endOfMonth()),

                // Thông tin filter
                'filterType'           => $filterType,
                'filterLabel'          => $filterLabel,
                'startDateFormatted'   => $start->format('d/m/Y'),
                'endDateFormatted'     => $end->format('d/m/Y'),
            ]));
        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            return $this->getFallbackData();
        }
    }

    // =============================================================================
    // CÁC HÀM MỚI: LỌC THEO NGÀY, TĂNG TRƯỞNG, CACHE
    // =============================================================================

    private function resolveDateRange($filterType, $startDate = null, $endDate = null)
    {
        $now = Carbon::now();

        return match ($filterType) {
            'today'       => [Carbon::today(), Carbon::today(), 'Hôm nay'],
            'yesterday'   => [Carbon::yesterday(), Carbon::yesterday(), 'Hôm qua'],
            'week'        => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek(), 'Tuần này'],
            'last_week'   => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek(), 'Tuần trước'],
            'month'       => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth(), 'Tháng này'],
            'last_month'  => [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth(), 'Tháng trước'],
            'custom'      => [
                $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::today(),
                $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::today(),
                $startDate && $endDate
                    ? Carbon::parse($startDate)->format('d/m') . ' → ' . Carbon::parse($endDate)->format('d/m/Y')
                    : 'Tùy chỉnh'
            ],
            default       => [Carbon::today(), Carbon::today(), 'Hôm nay'],
        };
    }

    private function getPreviousPeriod($start, $end)
    {
        $days = $start->diffInDays($end) + 1;
        return [
            'start' => $start->copy()->subDays($days),
            'end'   => $end->copy()->subDays($days),
        ];
    }

    private function getRevenueInRange($start, $end)
    {
        return DB::table('bills')
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'Paid')
            ->sum('final_amount') ?? 0;
    }

    private function getBillsInRange($start, $end)
    {
        return DB::table('bills')
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    private function getNewCustomersInRange($start, $end)
    {
        return DB::table('users')
            ->where('role_id', 4)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    private function getRevenueByDay($start, $end)
    {
        $raw = DB::table('bills')
            ->selectRaw('DATE(created_at) as date, SUM(final_amount) as revenue')
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'Paid')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('revenue', 'date');

        $result = [];
        $current = $start->copy();
        $daysOfWeek = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];

        while ($current->lte($end)) {
            $dateKey = $current->toDateString();
            $dayIndex = ($current->dayOfWeek + 6) % 7; // Thứ 2 = 0

            $result[] = [
                'day'       => $current->between($start->copy()->startOfWeek(), $start->copy()->endOfWeek())
                    ? $daysOfWeek[$dayIndex]
                    : $current->format('d/m'),
                'revenue'   => $raw[$dateKey] ?? 0,
                'full_date' => $current->format('d/m/Y')
            ];
            $current->addDay();
        }

        return $result;
    }

    private function getActiveEmployeesInRange($start, $end, $limit = 5)
    {
        return DB::table('bills')
            ->join('employees', 'bills.staff_id', '=', 'employees.user_id')
            ->select(
                'employees.id',
                'employees.name',
                DB::raw('COUNT(bills.id) as bill_count'),
                DB::raw('SUM(bills.final_amount) as total_revenue')
            )
            ->whereBetween('bills.created_at', [$start, $end])
            ->groupBy('employees.id', 'employees.name')
            ->orderByDesc('bill_count')
            ->limit($limit)
            ->get();
    }

    private function getTodayRevenue($today)
    {
        return $this->getRevenueInRange($today, $today);
    }

    private function getYesterdayRevenue()
    {
        $yesterday = Carbon::yesterday();
        return $this->getRevenueInRange($yesterday, $yesterday);
    }

    private function getTodayBills($today)
    {
        return $this->getBillsInRange($today, $today);
    }

    private function getYesterdayBills()
    {
        $yesterday = Carbon::yesterday();
        return $this->getBillsInRange($yesterday, $yesterday);
    }

    private function getFallbackData()
    {
        return view('admin.dashboard', [
            'todayRevenue' => 0,
            'todayBills' => 0,
            'newCustomersToday' => 0,
            'yesterdayRevenue' => 0,
            'yesterdayBills' => 0,
            'revenueGrowth' => 0,
            'billGrowth' => 0,
            'weeklyRevenue' => [],
            'topProducts' => [],
            'recentBills' => [],
            'activeEmployees' => [],
            'tableStats' => ['total' => 0, 'occupancy_rate' => 0],
            'lowStockProducts' => [],
            'shiftStats' => [],
            'monthlyStats' => ['revenue' => 0, 'bills' => 0, 'customers' => 0],
            'filterLabel' => 'Hôm nay'
        ]);
    }



    private function getTableStats()
    {
        return Cache::remember('dashboard.table_stats', 300, function () {
            $stats = DB::table('tables')
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN status = "occupied" THEN 1 ELSE 0 END) as occupied'),
                    DB::raw('SUM(CASE WHEN status = "reserved" THEN 1 ELSE 0 END) as reserved'),
                    DB::raw('SUM(CASE WHEN status = "maintenance" THEN 1 ELSE 0 END) as maintenance'),
                    DB::raw('SUM(CASE WHEN status = "available" THEN 1 ELSE 0 END) as available')
                )
                ->first();

            $total = $stats->total ?? 0;
            $occupied = $stats->occupied ?? 0;
            $reserved = $stats->reserved ?? 0;

            return [
                'total' => $total,
                'occupied' => $occupied,
                'reserved' => $reserved,
                'available' => $stats->available ?? 0,
                'maintenance' => $stats->maintenance ?? 0,
                'occupancy_rate' => $total > 0 ? (($occupied + $reserved) / $total) * 100 : 0
            ];
        });
    }

    /**
     * Lấy số khách hàng mới hôm nay (role customer)
     */
    private function getNewCustomersToday($today)
    {
        return DB::table('users')
            ->where('role_id', 4) // role_id 4 = Customer
            ->whereDate('created_at', $today)
            ->count();
    }

    /**
     * Lấy số khách hàng mới tháng này
     */
    private function getNewCustomersThisMonth($startOfMonth, $endOfMonth)
    {
        return DB::table('users')
            ->where('role_id', 4) // role_id 4 = Customer
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();
    }

    /**
     * Lấy doanh thu theo tuần (7 ngày gần nhất)
     */
    private function getWeeklyRevenue($startOfWeek, $endOfWeek)
    {
        $revenueData = DB::table('bills')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(final_amount) as revenue')
            )
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->where('payment_status', 'Paid')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Tạo mảng đầy đủ cho 7 ngày
        $weeklyRevenue = [];
        $currentDate = $startOfWeek->copy();

        $daysOfWeek = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];

        for ($i = 0; $i < 7; $i++) {
            $dateString = $currentDate->format('Y-m-d');
            $revenue = $revenueData->firstWhere('date', $dateString);

            $weeklyRevenue[] = [
                'day' => $daysOfWeek[$i],
                'revenue' => $revenue ? (float)$revenue->revenue : 0,
                'full_date' => $currentDate->format('d/m')
            ];

            $currentDate->addDay();
        }

        return $weeklyRevenue;
    }

    /**
     * Lấy top sản phẩm bán chạy từ bill_details - ĐÃ SỬA LỖI
     */
    private function getTopProducts($limit = 5)
    {
        return Cache::remember('dashboard.top_products', 600, function () use ($limit) {
            return DB::table('bill_details')
                ->join('products', 'bill_details.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select(
                    'products.name',
                    'categories.name as category_name',
                    DB::raw('SUM(bill_details.quantity) as total_quantity'),
                    DB::raw('SUM(bill_details.total_price) as total_revenue')
                )
                ->whereNotNull('bill_details.product_id')
                ->groupBy('products.id', 'products.name', 'categories.name')
                ->orderByDesc('total_quantity')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Lấy bill gần đây
     */
    private function getRecentBills($limit = 5)
    {
        return DB::table('bills')
            ->select('bills.*', 'tables.table_name', 'users.name as customer_name')
            ->leftJoin('tables', 'bills.table_id', '=', 'tables.id')
            ->leftJoin('users', 'bills.user_id', '=', 'users.id')
            ->orderByDesc('bills.created_at')
            ->limit($limit)
            ->get()
            ->map(function ($bill) {
                return [
                    'id' => $bill->id,
                    'bill_number' => $bill->bill_number,
                    'table_name' => $bill->table_name ?? 'Bàn ' . $bill->table_id,
                    'customer_name' => $bill->customer_name ?? 'Khách vãng lai',
                    'total_amount' => $bill->final_amount,
                    'status' => $bill->status,
                    'payment_status' => $bill->payment_status,
                    'created_at' => $bill->created_at,
                    'time_ago' => Carbon::parse($bill->created_at)->diffForHumans()
                ];
            });
    }

    /**
     * Lấy nhân viên tích cực (có nhiều bill nhất)
     */
    private function getActiveEmployees($limit = 3)
    {
        return DB::table('bills')
            ->join('employees', 'bills.staff_id', '=', 'employees.user_id')
            ->select(
                'employees.id',
                'employees.name',
                DB::raw('COUNT(bills.id) as bill_count'),
                DB::raw('SUM(bills.final_amount) as total_revenue')
            )
            ->whereDate('bills.created_at', Carbon::today())
            ->groupBy('employees.id', 'employees.name')
            ->orderByDesc('bill_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy thống kê đặt bàn
     */
    private function getReservationStats($today)
    {
        $totalReservations = DB::table('reservations')
            ->whereDate('reservation_time', $today)
            ->count();

        $confirmedReservations = DB::table('reservations')
            ->whereDate('reservation_time', $today)
            ->where('status', 'Confirmed')
            ->count();

        $completedReservations = DB::table('reservations')
            ->whereDate('reservation_time', $today)
            ->whereNotNull('checked_in_at')
            ->count();

        return [
            'total' => $totalReservations,
            'confirmed' => $confirmedReservations,
            'completed' => $completedReservations,
            'completion_rate' => $totalReservations > 0 ? ($completedReservations / $totalReservations) * 100 : 0
        ];
    }

    /**
     * Tính phần trăm tăng trưởng doanh thu
     */
    private function calculateRevenueGrowth($todayRevenue, $yesterdayRevenue)
    {
        if ($yesterdayRevenue == 0) {
            return $todayRevenue > 0 ? 100 : 0;
        }

        return (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100;
    }

    /**
     * Tính phần trăm tăng trưởng số bill
     */
    private function calculateBillGrowth($todayBills, $yesterdayBills)
    {
        if ($yesterdayBills == 0) {
            return $todayBills > 0 ? 100 : 0;
        }

        return (($todayBills - $yesterdayBills) / $yesterdayBills) * 100;
    }

    /**
     * API lấy dữ liệu cho biểu đồ
     */
    public function getChartData(Request $request)
    {
        $type = $request->get('type', 'weekly');

        switch ($type) {
            case 'monthly':
                $data = $this->getMonthlyRevenue();
                break;
            case 'yearly':
                $data = $this->getYearlyRevenue();
                break;
            default:
                $data = $this->getWeeklyRevenue(
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                );
        }

        return response()->json([
            'labels' => collect($data)->pluck('day')->toArray(),
            'revenues' => collect($data)->pluck('revenue')->toArray()
        ]);
    }

    /**
     * Lấy thống kê tháng này
     */
    private function getMonthlyStats($startOfMonth, $endOfMonth)
    {
        $revenue = DB::table('bills')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('payment_status', 'Paid')
            ->sum('final_amount') ?? 0;

        $bills = DB::table('bills')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $customers = DB::table('users')
            ->where('role_id', 4) // Customer
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        return [
            'revenue' => $revenue,
            'bills' => $bills,
            'customers' => $customers
        ];
    }

    /**
     * Lấy danh sách sản phẩm sắp hết hàng
     */
    private function getLowStockProducts($limit = 5)
    {
        return DB::table('products')
            ->whereRaw('stock_quantity <= min_stock_level')
            ->whereNull('deleted_at')
            ->orderBy('stock_quantity')
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy thống kê ca làm việc hôm nay
     */
    private function getShiftStats($today)
    {
        // Số nhân viên đang làm việc (active shift)
        $activeShifts = DB::table('employee_shifts')
            ->whereDate('shift_date', $today)
            ->where('status', 'active')
            ->count();

        // Tổng giờ làm việc hôm nay (của các shift đã kết thúc)
        $totalHours = DB::table('employee_shifts')
            ->whereDate('shift_date', $today)
            ->where('status', 'completed')
            ->sum('total_hours') ?? 0;

        // Danh sách nhân viên đang làm
        $workingEmployees = DB::table('employee_shifts')
            ->join('employees', 'employee_shifts.employee_id', '=', 'employees.id')
            ->whereDate('employee_shifts.shift_date', $today)
            ->where('employee_shifts.status', 'active')
            ->select('employees.name', 'employee_shifts.actual_start_time')
            ->limit(5)
            ->get();

        return [
            'active_count' => $activeShifts,
            'total_hours' => $totalHours,
            'working_employees' => $workingEmployees
        ];
    }
}
