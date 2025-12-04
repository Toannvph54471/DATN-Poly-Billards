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
            
            // Calculate precise hours if start/end times exist
            if ($shift->actual_start_time && $shift->actual_end_time) {
                $start = Carbon::parse($shift->actual_start_time);
                $end = Carbon::parse($shift->actual_end_time);
                
                // Handle overnight shift crossing midnight (if end < start, add 1 day to end)
                if ($end->lt($start)) {
                    $end->addDay();
                }
                
                $hours = $end->floatDiffInHours($start);
            }

            if ($employee->salary_type === 'monthly') {
                // Monthly employees: track hours but don't calculate per shift
                $shiftPay = 0; 
            } else {
                // Hourly calculation
                $baseRate = $baseSalary;
                
                // Manager minimum rate check
                if ($employee->position === 'manager' && $baseRate < 36000) {
                     $baseRate = 36000;
                }

                // Check for manual override multiplier
                if (isset($shift->shift->salary_multiplier) && $shift->shift->salary_multiplier > 1) {
                    // If manual multiplier exists (e.g. Holiday), use it for the whole shift
                    $shiftPay = $hours * $baseRate * $shift->shift->salary_multiplier;
                } else {
                    // Calculate Night Shift Split
                    // Night shift is 22:00 (10 PM) to 06:00 (6 AM)
                    $nightRateMultiplier = 1.3;
                    
                    $shiftPay = $this->calculateShiftPayWithNightDiff($start, $end, $baseRate, $nightRateMultiplier);
                }
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

    /**
     * Calculate pay splitting normal hours and night hours (22:00 - 06:00)
     */
    private function calculateShiftPayWithNightDiff(Carbon $start, Carbon $end, $rate, $nightMultiplier)
    {
        // Night window: 22:00 today -> 06:00 tomorrow
        // We need to handle shifts that might span multiple days or multiple night windows (unlikely but possible)
        // For simplicity, we assume shift is < 24 hours.
        
        $totalPay = 0;
        $current = $start->copy();
        
        // Construct Night Window for the "start day"
        $nightStart = $start->copy()->setTime(22, 0, 0);
        $nightEnd = $start->copy()->addDay()->setTime(6, 0, 0);
        
        // If shift starts after midnight (e.g. 01:00), the relevant night window started yesterday 22:00
        if ($start->hour < 6) {
            $nightStart = $start->copy()->subDay()->setTime(22, 0, 0);
            $nightEnd = $start->copy()->setTime(6, 0, 0);
        }
        
        $nightHours = 0;
        
        // Check window 1 (Yesterday night)
        $w1_start = $start->copy()->subDay()->setTime(22, 0, 0);
        $w1_end = $start->copy()->setTime(6, 0, 0);
        $nightHours += $this->getOverlap($start, $end, $w1_start, $w1_end);
        
        // Check window 2 (Today night)
        $w2_start = $start->copy()->setTime(22, 0, 0);
        $w2_end = $start->copy()->addDay()->setTime(6, 0, 0);
        $nightHours += $this->getOverlap($start, $end, $w2_start, $w2_end);
        
        // Total duration
        $totalDuration = $end->floatDiffInHours($start);
        $normalHours = $totalDuration - $nightHours;
        
        // Sanity check (floating point)
        if ($normalHours < 0) $normalHours = 0;
        
        return ($normalHours * $rate) + ($nightHours * $rate * $nightMultiplier);
    }

    private function getOverlap(Carbon $start, Carbon $end, Carbon $rangeStart, Carbon $rangeEnd)
    {
        if ($end->lte($rangeStart) || $start->gte($rangeEnd)) {
            return 0;
        }
        
        $overlapStart = $start->max($rangeStart);
        $overlapEnd = $end->min($rangeEnd);
        
        return $overlapEnd->floatDiffInHours($overlapStart);
    }
}
