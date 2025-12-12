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

        $this->checkLocked($request->month);

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
        
        $this->checkLocked($payroll->period);
        
        // Reuse generate logic
        $updatedPayroll = $this->payrollService->createPayroll(
            $payroll->employee_id,
            $payroll->period,
            [
                'bonus' => $request->bonus ?? $payroll->bonus,
                'deductions' => $request->deductions ?? $payroll->deductions,
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

    public function lockMonth(Request $request)
    {
        $request->validate([
             'month' => 'required|date_format:Y-m',
        ]);

        $month = $request->month;

        // DB Transaction
        \Illuminate\Support\Facades\DB::transaction(function() use ($month) {
             // Lock Payrolls
             Payroll::where('period', $month)
                 ->update([
                     'is_locked' => true,
                     'locked_at' => now()
                 ]);

             // Log
             \App\Models\ActivityLog::log('lock_payroll', "Locked payroll for month {$month}");
        });

        return response()->json(['status' => 'success', 'message' => "Đã chốt bảng lương tháng {$month}."]);
    }

    private function checkLocked($period)
    {
        $exists = Payroll::where('period', $period)->where('is_locked', true)->exists();
        if ($exists) {
            abort(403, "Bảng lương tháng {$period} đã bị khóa. Không thể chỉnh sửa.");
        }
    }

    public function adminIndex(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        
        $employees = \App\Models\Employee::with(['payrolls' => function($query) use ($month) {
            $query->where('period', $month);
        }])->paginate(10);

        // Check if locked
        $isLocked = Payroll::where('period', $month)->where('is_locked', true)->exists();

        return view('admin.payroll.index', compact('employees', 'month', 'isLocked'));
    }
    public function generateAll(Request $request) {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $this->checkLocked($request->month);
        
        $employees = \App\Models\Employee::all();
        $count = 0;
        $skipped = 0;

        foreach ($employees as $employee) {
            // Check if existing payroll is manual
            $existing = Payroll::where('employee_id', $employee->id)
                ->where('period', $request->month)
                ->first();
                
            if ($existing && $existing->is_manual) {
                $skipped++;
                continue;
            }

            // Only calculate if not paid?
            if ($existing && $existing->status === Payroll::STATUS_PAID) {
                // Don't recalculate paid ones
                $skipped++;
                continue;
            }

            $this->payrollService->createPayroll($employee->id, $request->month);
            $count++;
        }
        
        \App\Models\ActivityLog::log('calculate_all_payroll', "Calculated payroll for {$count} employees for month {$request->month}. Skipped {$skipped} manual/paid records.");

        return response()->json([
            'status' => 'success',
            'message' => "Đã tính lương cho {$count} nhân viên. Bỏ qua {$skipped} bản ghi (đã chỉnh sửa thủ công hoặc đã thanh toán)."
        ]);
    }

    public function markAsPaid(Request $request, $id) {
        $payroll = Payroll::findOrFail($id);
        
        $this->checkLocked($payroll->period);

        $payroll->status = Payroll::STATUS_PAID;
        $payroll->save();
        
        \App\Models\ActivityLog::log('pay_payroll', "Marked payroll paid for employee {$payroll->employee_id} month {$payroll->period}");

        return response()->json([
            'status' => 'success',
            'message' => 'Đã xác nhận thanh toán lương.'
        ]);
    }

    public function payAll(Request $request) {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $this->checkLocked($request->month);

        $count = Payroll::where('period', $request->month)
            ->where('status', '!=', Payroll::STATUS_PAID)
            ->update(['status' => Payroll::STATUS_PAID]);
            
        \App\Models\ActivityLog::log('pay_all_payroll', "Marked all payrolls paid for month {$request->month} ({$count} records)");

        return response()->json([
            'status' => 'success',
            'message' => "Đã xác nhận thanh toán cho {$count} nhân viên."
        ]);
    }
}
