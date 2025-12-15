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

            // Lấy dữ liệu cơ bản
            $basicData = $this->getBasicData($start, $end);

            // Lấy dữ liệu chart
            $chartData = $this->getChartData($start, $end);

            // Lấy dữ liệu bổ sung (không bao gồm nhân viên)
            $extras = $this->getCachedExtras($start, $end);

            // Lấy thống kê bàn
            $tableStats = $this->getTableStats();

            // Lấy thống kê sản phẩm
            $productStats = $this->getProductStats();

            // Tổng hợp dữ liệu
            $data = array_merge([
                'filterType'         => $filterType,
                'filterLabel'        => $filterLabel,
                'startDateFormatted' => $start->format('d/m/Y'),
                'endDateFormatted'   => $end->format('d/m/Y'),
                'start'              => $start,
                'end'                => $end,
            ], $basicData, $chartData, $extras, $tableStats, $productStats);

            return view('admin.dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('admin.dashboard', $this->getFallbackData());
        }
    }

    // Lấy dữ liệu cơ bản (doanh thu, đơn hàng)
    private function getBasicData(Carbon $start, Carbon $end)
    {
        // Doanh thu đã thanh toán
        $revenuePaid = DB::table('bills')
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'Paid')
            ->sum('final_amount') ?? 0;

        // Doanh thu đang chờ (đơn mở)
        $revenueExpected = DB::table('bills')
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', '!=', 'Paid')
            ->where('status', 'Open')
            ->sum('total_amount') ?? 0;

        $totalRevenue = $revenuePaid + $revenueExpected;

        // Số đơn đã thanh toán
        $billsPaid = DB::table('bills')
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'Paid')
            ->count();

        // Số đơn đang chờ
        $billsExpected = DB::table('bills')
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', '!=', 'Paid')
            ->where('status', 'Open')
            ->count();

        $totalBills = $billsPaid + $billsExpected;

        // Chi tiết đơn đã thanh toán
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

    // Lấy dữ liệu cho biểu đồ
    private function getChartData(Carbon $start, Carbon $end)
    {
        $daysDiff = $start->diffInDays($end);
        $current = $start->copy();

        $labels = [];
        $paidData = [];
        $expectedData = [];
        $allData = [];

        while ($current->lte($end)) {
            $dayStart = $current->copy()->startOfDay();
            $dayEnd = $current->copy()->endOfDay();

            // Doanh thu đã thanh toán trong ngày
            $paid = DB::table('bills')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->where('payment_status', 'Paid')
                ->sum('final_amount') ?? 0;

            // Doanh thu đang chờ trong ngày
            $expected = DB::table('bills')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->where('payment_status', '!=', 'Paid')
                ->where('status', 'Open')
                ->sum('total_amount') ?? 0;

            // Định dạng label
            if ($daysDiff <= 7) {
                $label = $this->getVietnameseDayName($current->dayOfWeek);
            } elseif ($daysDiff <= 31) {
                $label = $current->format('d/m');
            } else {
                $label = 'Tuần ' . $current->weekOfYear;
            }

            $labels[] = $label;
            $paidData[] = $paid;
            $expectedData[] = $expected;
            $allData[] = $paid + $expected;

            $current->addDay();
        }

        // Tính toán thống kê
        $stats = [
            'paid_max' => $paidData ? max($paidData) : 0,
            'paid_avg' => $paidData ? round(array_sum($paidData) / count($paidData), 0) : 0,
            'paid_total' => array_sum($paidData),
            'expected_max' => $expectedData ? max($expectedData) : 0,
            'expected_avg' => $expectedData ? round(array_sum($expectedData) / count($expectedData), 0) : 0,
            'expected_total' => array_sum($expectedData),
            'all_max' => $allData ? max($allData) : 0,
            'all_avg' => $allData ? round(array_sum($allData) / count($allData), 0) : 0,
            'all_total' => array_sum($allData),
        ];

        return [
            'chartLabels' => $labels,
            'chartData'   => [
                'paid' => $paidData,
                'expected' => $expectedData,
                'all' => $allData
            ],
            'chartStats'  => $stats,
        ];
    }

    // Lấy dữ liệu bổ sung (cache 5 phút)
    private function getCachedExtras(Carbon $start, Carbon $end)
    {
        $key = 'dashboard_extras_' . md5($start->format('Ymd') . $end->format('Ymd'));

        return Cache::remember($key, now()->addMinutes(5), function () {
            return [
                'recentBills' => $this->getRecentBills(10),
                'topProducts' => $this->getTopProducts(5),
            ];
        });
    }

    // Lấy thống kê bàn
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
        $used = ($stats->occupied ?? 0) + ($stats->reserved ?? 0);

        return [
            'tableStats' => [
                'total' => $total,
                'occupied' => $stats->occupied ?? 0,
                'reserved' => $stats->reserved ?? 0,
                'available' => $stats->available ?? 0,
                'maintenance' => $stats->maintenance ?? 0,
                'occupancy_rate' => $total > 0 ? round(($used / $total) * 100, 2) : 0,
            ]
        ];
    }

    // Lấy thống kê sản phẩm
    private function getProductStats()
    {
        return [
            'lowStockProducts' => $this->getLowStockProducts(5),
            'bestSellingProducts' => $this->getBestSellingProducts(),
        ];
    }

    // Lấy sản phẩm bán chạy nhất
    private function getBestSellingProducts()
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();

        // Sản phẩm bán chạy hôm nay
        $todayBest = DB::table('bill_details')
            ->join('bills', 'bill_details.bill_id', '=', 'bills.id')
            ->join('products', 'bill_details.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(bill_details.quantity) as total_quantity'),
                DB::raw('SUM(bill_details.total_price) as total_revenue')
            )
            ->whereDate('bills.created_at', $today)
            ->where('bills.payment_status', 'Paid')
            ->whereNotNull('bill_details.product_id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(3)
            ->get();

        // Sản phẩm bán chạy tuần này
        $weekBest = DB::table('bill_details')
            ->join('bills', 'bill_details.bill_id', '=', 'bills.id')
            ->join('products', 'bill_details.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(bill_details.quantity) as total_quantity'),
                DB::raw('SUM(bill_details.total_price) as total_revenue')
            )
            ->whereBetween('bills.created_at', [$weekStart, $today->endOfDay()])
            ->where('bills.payment_status', 'Paid')
            ->whereNotNull('bill_details.product_id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(3)
            ->get();

        return [
            'today' => $todayBest,
            'week' => $weekBest
        ];
    }

    // Chi tiết đơn đã thanh toán
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
                    'time_ago' => Carbon::parse($bill->created_at)->diffForHumans(),
                    'formatted_date' => Carbon::parse($bill->created_at)->format('H:i d/m/Y'),
                ];
            });
    }

    // Lấy top sản phẩm
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

    // Lấy đơn hàng gần đây
    private function getRecentBills($limit = 10)
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
                    'time_ago' => Carbon::parse($bill->created_at)->diffForHumans(),
                ];
            });
    }

    // Lấy sản phẩm sắp hết hàng
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

    // Helper: Tên ngày tiếng Việt
    private function getVietnameseDayName($dayOfWeek)
    {
        $days = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        return $days[$dayOfWeek] ?? 'Ngày';
    }

    // Xác định khoảng thời gian
    private function resolveDateRange($type, $startDate = null, $endDate = null)
    {
        $now = Carbon::now();

        return match ($type) {
            'yesterday' => [
                $now->clone()->subDay()->startOfDay(),
                $now->clone()->subDay()->endOfDay(),
                'Hôm qua (' . $now->clone()->subDay()->format('d/m/Y') . ')'
            ],
            'week' => [
                $now->clone()->startOfWeek(),
                $now->clone()->endOfWeek(),
                'Tuần này (' . $now->startOfWeek()->format('d/m') . ' - ' . $now->endOfWeek()->format('d/m/Y') . ')'
            ],
            'last_week' => [
                $now->clone()->subWeek()->startOfWeek(),
                $now->clone()->subWeek()->endOfWeek(),
                'Tuần trước (' . $now->subWeek()->startOfWeek()->format('d/m') . ' - ' . $now->subWeek()->endOfWeek()->format('d/m/Y') . ')'
            ],
            'custom' => [
                $startDate ? Carbon::parse($startDate)->startOfDay() : $now->startOfDay(),
                $endDate ? Carbon::parse($endDate)->endOfDay() : $now->endOfDay(),
                'Từ ' . Carbon::parse($startDate ?? now())->format('d/m') . ' → ' . Carbon::parse($endDate ?? now())->format('d/m/Y')
            ],
            default => [
                $now->clone()->startOfDay(),
                $now->clone()->endOfDay(),
                'Hôm nay (' . $now->format('d/m/Y') . ')'
            ],
        };
    }

    // Dữ liệu fallback khi có lỗi
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
            'chartStats' => [
                'paid_max' => 0,
                'paid_avg' => 0,
                'paid_total' => 0,
                'expected_max' => 0,
                'expected_avg' => 0,
                'expected_total' => 0,
                'all_max' => 0,
                'all_avg' => 0,
                'all_total' => 0
            ],
            'paidBillsDetails' => collect(),
            'topProducts' => collect(),
            'recentBills' => collect(),
            'lowStockProducts' => collect(),
            'bestSellingProducts' => ['today' => collect(), 'week' => collect()],
            'tableStats' => [
                'total' => 0,
                'occupied' => 0,
                'reserved' => 0,
                'available' => 0,
                'maintenance' => 0,
                'occupancy_rate' => 0
            ],
            'filterLabel' => 'Hôm nay',
            'filterType' => 'today',
            'startDateFormatted' => now()->format('d/m/Y'),
            'endDateFormatted' => now()->format('d/m/Y'),
        ];
    }

    public function refreshData(Request $request)
    {
        try {
            $filterType = $request->get('filter', 'today');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            [$start, $end] = $this->resolveDateRange($filterType, $startDate, $endDate);
            $key = 'dashboard_extras_' . md5($start->format('Ymd') . $end->format('Ymd'));
            Cache::forget($key);

            return response()->json(['success' => true, 'message' => 'Đã làm mới dữ liệu']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi khi làm mới'], 500);
        }
    }
}
