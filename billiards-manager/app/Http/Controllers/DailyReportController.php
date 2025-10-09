<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DailyReportController extends Controller
{
    public function index()
    {
        $dailyReports = DailyReport::latest()->get();
        return view('daily-reports.index', compact('dailyReports'));
    }

    public function create()
    {
        return view('daily-reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'report_date' => 'required|date',
            'total_revenue' => 'required|numeric|min:0',
            'total_customers' => 'required|integer|min:0',
            'total_bills' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        DailyReport::create($request->all());

        return redirect()->route('daily-reports.index')
            ->with('success', 'Daily report created successfully.');
    }

    public function show(DailyReport $dailyReport)
    {
        return view('daily-reports.show', compact('dailyReport'));
    }

    public function edit(DailyReport $dailyReport)
    {
        return view('daily-reports.edit', compact('dailyReport'));
    }

    public function update(Request $request, DailyReport $dailyReport)
    {
        $request->validate([
            'report_date' => 'required|date',
            'total_revenue' => 'required|numeric|min:0',
            'total_customers' => 'required|integer|min:0',
            'total_bills' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $dailyReport->update($request->all());

        return redirect()->route('daily-reports.index')
            ->with('success', 'Daily report updated successfully.');
    }

    public function destroy(DailyReport $dailyReport)
    {
        $dailyReport->delete();

        return redirect()->route('daily-reports.index')
            ->with('success', 'Daily report deleted successfully.');
    }

    public function generateReport(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        
        // Logic để generate report tự động
        // Tính tổng doanh thu, số khách, số hóa đơn trong ngày
        
        return redirect()->route('daily-reports.index')
            ->with('success', 'Daily report generated successfully.');
    }
}