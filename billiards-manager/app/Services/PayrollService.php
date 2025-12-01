<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    public function calculateMonthlySalary($employeeId, $month)
    {
        // $month format: 'Y-m' e.g., '2025-11'
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $employee = Employee::findOrFail($employeeId);
        
        // Get all completed shifts for the employee in the given month
        $shifts = EmployeeShift::with('shift')
            ->where('employee_id', $employeeId)
            ->where('status', 'Completed')
            ->whereBetween('shift_date', [$startOfMonth, $endOfMonth])
            ->get();

        $totalHours = 0;
        $totalAmount = 0;
        $baseSalary = $employee->salary_rate; // Hourly rate or Monthly rate

        // For Monthly Salary Logic
        $standardWorkingDays = 26; // Standard working days per month
        $workedDays = $shifts->pluck('shift_date')->unique()->count();

        foreach ($shifts as $shift) {
            $hours = $shift->total_hours ?? 0;
            
            if ($hours == 0 && $shift->actual_start_time && $shift->actual_end_time) {
                $start = Carbon::parse($shift->actual_start_time);
                $end = Carbon::parse($shift->actual_end_time);
                $hours = $end->floatDiffInHours($start);
            }

            $multiplier = $shift->shift->salary_multiplier ?? 1.0;
            
            if ($employee->salary_type === 'monthly') {
                // Monthly employees: track hours but don't calculate per shift
                $shiftPay = 0; 
            } else {
                // Hourly calculation
                // Check if Manager
                $rate = $baseSalary;
                if ($employee->position === 'manager' && $rate < 36000) {
                     $rate = 36000;
                }

                $shiftPay = $hours * $rate * $multiplier;
            }
            
            $totalHours += $hours;
            $totalAmount += $shiftPay;
        }

        // If monthly, calculate pro-rated salary
        if ($employee->salary_type === 'monthly') {
            // Formula: (Salary / StandardDays) * WorkedDays
            // Ensure we don't pay more than base salary if they worked more days (unless policy says so)
            // For now, let's allow it to exceed if they work extra days (e.g. 27/26)
            $dailyRate = $baseSalary / $standardWorkingDays;
            $totalAmount = $dailyRate * $workedDays;
        }

        return [
            'total_hours' => $totalHours,
            'total_amount' => round($totalAmount, 2),
            'period' => $month,
            'worked_days' => $workedDays // Return this for info
        ];
    }

    public function createPayroll($employeeId, $month, $data = [])
    {
        $calculation = $this->calculateMonthlySalary($employeeId, $month);
        
        $bonus = $data['bonus'] ?? 0;
        $deductions = $data['deductions'] ?? 0;
        $notes = $data['notes'] ?? null;

        $finalAmount = $calculation['total_amount'] + $bonus - $deductions;

        return Payroll::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'period' => $month
            ],
            [
                'total_hours' => $calculation['total_hours'],
                'base_salary' => Employee::find($employeeId)->salary_rate,
                'total_amount' => $finalAmount,
                'bonus' => $bonus,
                'deductions' => $deductions,
                'notes' => $notes,
                'status' => 'Calculated'
            ]
        );
    }
}
