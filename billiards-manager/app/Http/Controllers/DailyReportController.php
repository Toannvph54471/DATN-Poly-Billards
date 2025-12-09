<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyReportController extends Controller
{
    // Hiển thị danh sách báo cáo
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Validate dates
        if ($startDate && $endDate) {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);
        }

        // Lấy dữ liệu
        $reports = DailyReport::getDailyReports($perPage, $startDate, $endDate);

        // Tính tổng
        $summary = $this->calculateSummary($reports->items());

        return view('daily_reports.index', compact('reports', 'summary', 'startDate', 'endDate'));
    }

    // Hiển thị chi tiết một ngày
    public function show($date)
    {
        $report = DailyReport::where('report_date', $date)->firstOrFail();

        // Lấy chi tiết hóa đơn trong ngày
        $bills = DB::table('bills as b')
            ->leftJoin('users as u', 'b.user_id', '=', 'u.id')
            ->leftJoin('tables as t', 'b.table_id', '=', 't.id')
            ->select(
                'b.bill_number',
                't.table_name',
                'u.name as customer_name',
                DB::raw("COALESCE(u.name, 'Khách vãng lai') as customer_display"),
                'b.total_amount',
                'b.final_amount',
                'b.discount_amount',
                'b.payment_method',
                'b.created_at',
                'b.end_time'
            )
            ->where('b.status', 'Closed')
            ->where('b.payment_status', 'Paid')
            ->whereDate('b.created_at', $date)
            ->orderBy('b.created_at', 'desc')
            ->get();

        return view('daily_reports.show', compact('report', 'bills', 'date'));
    }

    // Tạo báo cáo thủ công
    public function generate(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        try {
            $report = DailyReport::generateReportForDate($request->date);

            return redirect()
                ->route('daily-reports.show', $report->report_date)
                ->with('success', 'Báo cáo đã được tạo thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi tạo báo cáo: ' . $e->getMessage());
        }
    }

    // Xuất Excel
    public function export(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = DailyReport::query()->orderBy('report_date', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('report_date', [$startDate, $endDate]);
        }

        $reports = $query->get();

        // Sử dụng Maatwebsite/Laravel-Excel package
        // Hoặc trả về CSV đơn giản
        return $this->exportToCSV($reports);
    }

    // Dashboard - Thống kê tổng quan
    public function dashboard()
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $thisMonth = date('Y-m');
        $lastMonth = date('Y-m', strtotime('-1 month'));

        // Doanh thu hôm nay
        $todayRevenue = DailyReport::where('report_date', $today)
            ->value('total_revenue') ?? 0;

        // Doanh thu hôm qua
        $yesterdayRevenue = DailyReport::where('report_date', $yesterday)
            ->value('total_revenue') ?? 0;

        // Doanh thu tháng này
        $monthRevenue = DailyReport::whereYear('report_date', date('Y'))
            ->whereMonth('report_date', date('m'))
            ->sum('total_revenue');

        // Doanh thu tháng trước
        $lastMonthRevenue = DailyReport::whereYear('report_date', date('Y', strtotime('-1 month')))
            ->whereMonth('report_date', date('m', strtotime('-1 month')))
            ->sum('total_revenue');

        // Tổng số hóa đơn tháng này
        $monthBills = DailyReport::whereYear('report_date', date('Y'))
            ->whereMonth('report_date', date('m'))
            ->sum('total_bills');

        // Doanh thu theo tuần (7 ngày gần nhất)
        $weeklyRevenue = DailyReport::where('report_date', '>=', date('Y-m-d', strtotime('-7 days')))
            ->orderBy('report_date')
            ->get(['report_date', 'total_revenue', 'total_bills']);

        // Top 5 ngày doanh thu cao nhất
        $topDays = DailyReport::orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        return view('daily_reports.dashboard', compact(
            'todayRevenue',
            'yesterdayRevenue',
            'monthRevenue',
            'lastMonthRevenue',
            'monthBills',
            'weeklyRevenue',
            'topDays'
        ));
    }

    // Helper: Tính tổng hợp
    private function calculateSummary($reports)
    {
        return [
            'total_revenue' => collect($reports)->sum('total_revenue'),
            'total_discount' => collect($reports)->sum('total_discount'),
            'total_bills' => collect($reports)->sum('total_bills'),
            'total_customers' => collect($reports)->sum('total_customers'),
            'avg_bill_value' => collect($reports)->avg('average_bill_value')
        ];
    }

    // Helper: Xuất CSV
    private function exportToCSV($reports)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="daily_reports_' . date('Ymd') . '.csv"',
        ];

        $callback = function () use ($reports) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'Ngày',
                'Doanh thu (VND)',
                'Giảm giá (VND)',
                'Số hóa đơn',
                'Số khách hàng',
                'Giá trị TB hóa đơn'
            ]);

            // Data
            foreach ($reports as $report) {
                fputcsv($file, [
                    $report->report_date,
                    number_format($report->total_revenue, 0, ',', '.'),
                    number_format($report->total_discount, 0, ',', '.'),
                    $report->total_bills,
                    $report->total_customers,
                    number_format($report->average_bill_value, 0, ',', '.')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
