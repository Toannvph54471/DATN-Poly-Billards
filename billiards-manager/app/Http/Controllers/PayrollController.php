<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Services\PayrollService;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function index()
    {
        $payrolls = Payroll::with('employee')->latest()->paginate(10);
        return response()->json($payrolls);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|date_format:Y-m',
            'bonus' => 'nullable|numeric|min:0',
            'penalty' => 'nullable|numeric|min:0',
        ]);

        $payroll = $this->payrollService->createPayroll(
            $request->employee_id,
            $request->month,
            $request->all()
        );

        return response()->json([
            'message' => 'Payroll generated successfully',
            'data' => $payroll
        ]);
    }

    public function recalculate(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);
        
        // Reuse generate logic
        $updatedPayroll = $this->payrollService->createPayroll(
            $payroll->employee_id,
            $payroll->period,
            [
                'bonus' => $request->bonus ?? $payroll->bonus,
                'penalty' => $request->penalty ?? $payroll->penalty, // Use existing or new
                'notes' => $request->notes ?? $payroll->notes
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Payroll recalculated successfully',
            'data' => $updatedPayroll
        ]);
    }

    public function show($id)
    {
        $payroll = Payroll::with('employee')->findOrFail($id);
        return response()->json($payroll);
    }

    public function adminIndex(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        
        $employees = \App\Models\Employee::with(['payrolls' => function($query) use ($month) {
            $query->where('period', $month);
        }])->paginate(10);

        return view('admin.payroll.index', compact('employees', 'month'));
    }
}
