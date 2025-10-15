<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Table;
use App\Models\Customer;
use App\Models\Product;
use App\Models\DailyReport;
use App\Models\EmployeeShift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        // Thống kê tổng quan
        $stats = [
            'total_revenue_today' => Bill::whereDate('created_at', $today)
                ->where('status', 'Closed')
                ->sum('final_amount'),
            'total_bills_today' => Bill::whereDate('created_at', $today)->count(),
            'active_tables' => Table::where('status', 'InUse')->count(),
            'available_tables' => Table::where('status', 'Available')->count(),
            'total_customers_today' => Bill::whereDate('created_at', $today)
                ->whereNotNull('customer_id')
                ->distinct('customer_id')
                ->count('customer_id'),
        ];

        // Bàn đang hoạt động
        $activeTables = Table::with(['currentBill' => function($query) {
            $query->with('customer');
        }])
        ->where('status', 'InUse')
        ->get();

        // Nhân viên đang làm việc
        $workingStaff = EmployeeShift::with(['employee.user'])
            ->whereDate('shift_date', $today)
            ->where('status', 'Working')
            ->get();

        // Sản phẩm sắp hết
        $lowStockProducts = Product::whereRaw('stock_quantity <= min_stock_level')
            ->where('status', 'Active')
            ->get();

        // Doanh thu 7 ngày gần nhất
        $revenueLast7Days = DailyReport::where('report_date', '>=', $today->subDays(7))
            ->orderBy('report_date')
            ->get(['report_date', 'total_revenue']);

        return view('dashboard.index', compact(
            'stats', 
            'activeTables', 
            'workingStaff', 
            'lowStockProducts',
            'revenueLast7Days'
        ));
    }

    public function reports(Request $request)
    {
        $startDate = $request->get('start_date', today()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', today()->format('Y-m-d'));

        $reports = DailyReport::whereBetween('report_date', [$startDate, $endDate])
            ->orderBy('report_date')
            ->get();

        $totalRevenue = $reports->sum('total_revenue');
        $totalBills = $reports->sum('total_bills');
        $totalCustomers = $reports->sum('total_customers');

        // Top sản phẩm bán chạy
        $topProducts = Product::withCount(['billDetails as total_sold' => function($query) use ($startDate, $endDate) {
            $query->whereHas('bill', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', 'Closed');
            });
        }])
        ->orderBy('total_sold', 'desc')
        ->limit(10)
        ->get();

        return view('dashboard.reports', compact(
            'reports',
            'totalRevenue',
            'totalBills',
            'totalCustomers',
            'topProducts',
            'startDate',
            'endDate'
        ));
    }
}