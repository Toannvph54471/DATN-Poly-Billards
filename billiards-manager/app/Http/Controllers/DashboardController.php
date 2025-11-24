<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $today = Carbon::today();
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            // Doanh thu hôm nay từ bảng bills
            $todayRevenue = $this->getTodayRevenue($today);
            $yesterdayRevenue = $this->getYesterdayRevenue();
            $revenueGrowth = $this->calculateRevenueGrowth($todayRevenue, $yesterdayRevenue);

            // Số bill hôm nay
            $todayBills = $this->getTodayBills($today);
            $yesterdayBills = $this->getYesterdayBills();
            $billGrowth = $this->calculateBillGrowth($todayBills, $yesterdayBills);

            // Thống kê bàn
            $tableStats = $this->getTableStats();

            // Khách hàng mới (từ bảng users với role customer)
            $newCustomersToday = $this->getNewCustomersToday($today);
            $newCustomersThisMonth = $this->getNewCustomersThisMonth($startOfMonth, $endOfMonth);

            // Doanh thu theo tuần
            $weeklyRevenue = $this->getWeeklyRevenue($startOfWeek, $endOfWeek);

            // Sản phẩm bán chạy từ bill_details
            $topProducts = $this->getTopProducts(5);

            // Bill gần đây
            $recentBills = $this->getRecentBills(5);

            // Nhân viên tích cực
            $activeEmployees = $this->getActiveEmployees(3);

            // Thống kê đặt bàn
            $reservationStats = $this->getReservationStats($today);

            // [NEW] Thống kê tháng
            $monthlyStats = $this->getMonthlyStats($startOfMonth, $endOfMonth);

            // [NEW] Cảnh báo kho
            $lowStockProducts = $this->getLowStockProducts(5);

            // [NEW] Ca làm việc
            $shiftStats = $this->getShiftStats($today);

            return view('admin.dashboard', compact(
                'todayRevenue',
                'yesterdayRevenue',
                'revenueGrowth',
                'todayBills',
                'yesterdayBills',
                'billGrowth',
                'tableStats',
                'newCustomersToday',
                'newCustomersThisMonth',
                'weeklyRevenue',
                'topProducts',
                'recentBills',
                'activeEmployees',
                'reservationStats',
                'monthlyStats',
                'lowStockProducts',
                'shiftStats'
            ));
        } catch (\Exception $e) {
            // Fallback data nếu có lỗi
            return $this->getFallbackData();
        }
    }

    /**
     * Lấy doanh thu hôm nay từ bảng bills
     */
    private function getTodayRevenue($today)
    {
        return DB::table('bills')
            ->whereDate('created_at', $today)
            ->where('payment_status', 'Paid')
            ->sum('final_amount') ?? 0;
    }

    /**
     * Lấy doanh thu hôm qua
     */
    private function getYesterdayRevenue()
    {
        $yesterday = Carbon::yesterday();

        return DB::table('bills')
            ->whereDate('created_at', $yesterday)
            ->where('payment_status', 'Paid')
            ->sum('final_amount') ?? 0;
    }

    /**
     * Lấy số bill hôm nay
     */
    private function getTodayBills($today)
    {
        return DB::table('bills')
            ->whereDate('created_at', $today)
            ->count();
    }

    /**
     * Lấy số bill hôm qua
     */
    private function getYesterdayBills()
    {
        $yesterday = Carbon::yesterday();

        return DB::table('bills')
            ->whereDate('created_at', $yesterday)
            ->count();
    }

    /**
     * Lấy thống kê tình trạng bàn
     */
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
