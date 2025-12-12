<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use Carbon\Carbon;

class PayrollService
{
    /**
     * Calculate monthly salary based on hourly rate and total minutes worked.
     */
    public function calculateMonthlySalary($employeeId, $month)
    {
        // $month format: 'Y-m' e.g., '2025-11'
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $employee = Employee::findOrFail($employeeId);
        
        // Get all approved/valid attendance records
        // We include records that are 'Late' but NOT 'rejected'.
        // If 'Late' and 'approved', do we waive the penalty?
        // User rule: "Tự động trừ 20.000đ cho mỗi lần đi muộn". 
        // Likely implying if it IS late (status="Late"), we charge unless maybe explicitly cleared (status changed to Present?).
        // If 'approval_status' is 'approved', let's assume waiver (or maybe user wants fixed penalty regardless?).
        // Let's assume: If status == 'Late' AND approval_status != 'approved', then Penalty.
        
        $attendances = Attendance::where('employee_id', $employeeId)
            ->where('approval_status', '!=', 'rejected')
            ->whereBetween('check_in', [$startOfMonth, $endOfMonth])
            ->get();

        $totalMinutes = 0;
        $lateCount = 0;
        $latePenalty = 0;
        $LATE_FEE_PER_SESSION = 20000;

        foreach ($attendances as $attendance) {
            // Calculate minutes
            $minutes = max(0, (float)$attendance->total_minutes);
            if (!$minutes && $attendance->check_out) {
                // ... (existing helper logic if total_minutes blank)
                 $checkIn = Carbon::parse($attendance->check_in);
                 $checkOut = Carbon::parse($attendance->check_out);
                 $minutes = $checkOut->diffInMinutes($checkIn);
            }
            $totalMinutes += max(0, $minutes);

            // Calculate Late Penalty
            // If status is 'Late' and NOT approved (waived)
            if ($attendance->status === 'Late' && $attendance->approval_status !== 'approved') {
                $lateCount++;
            }
        }
        
        $latePenalty = $lateCount * $LATE_FEE_PER_SESSION;

        $totalHours = $totalMinutes / 60;
        $hourlyRate = $employee->hourly_rate;
        $baseAmount = $totalHours * $hourlyRate;
        
        $baseAmount = max(0, $baseAmount);

        return [
            'total_minutes' => $totalMinutes,
            'total_hours' => round($totalHours, 2),
            'hourly_rate' => $hourlyRate,
            'base_amount' => round($baseAmount, 2),
            'late_count' => $lateCount,
            'late_penalty' => $latePenalty,
            'period' => $month
        ];
    }

    public function createPayroll($employeeId, $month, $data = [])
    {
        $calculation = $this->calculateMonthlySalary($employeeId, $month);
        
        // Allow manual override
        $totalHours = isset($data['total_hours']) ? max(0, (float)$data['total_hours']) : $calculation['total_hours'];
        $hourlyRate = isset($data['hourly_rate']) ? max(0, (float)$data['hourly_rate']) : $calculation['hourly_rate'];
        
        // Recalculate base amount if overridden
        $baseAmount = max(0, $totalHours * $hourlyRate);

        // Find existing payroll to preserve data
        $existingPayroll = Payroll::where('employee_id', $employeeId)
            ->where('period', $month)
            ->first();

        // 1. Bonus: Use passed data OR existing OR 0
        $bonus = isset($data['bonus']) ? (float)$data['bonus'] : ($existingPayroll->bonus ?? 0);
        
        // 2. Notes: Use passed data OR existing OR null
        $notes = isset($data['notes']) ? $data['notes'] : ($existingPayroll->notes ?? null);

        // 3. Penalty (Deductions/Fine) - NOT Late Penalty
        // Use passed 'deductions' or 'penalty' (map to deductions column)
        // Note: DB has 'deductions' and 'penalty' column? 
        // Migration 2025_11_20_210507 added 'bonus', 'deductions'. Previous table had 'penalty'.
        // Let's assume 'deductions' is the editable Fine field.
        // 'late_penalty' is the calculated Late Fee.
        
        $deductions = isset($data['deductions']) ? (float)$data['deductions'] : ($existingPayroll->deductions ?? 0);

        // Calculated Late Penalty
        $lateCount = $calculation['late_count'];
        $latePenalty = $calculation['late_penalty'];

        // Total Subtract
        // Final = Base + Bonus - Deductions - LatePenalty
        $finalAmount = ($baseAmount + $bonus) - $deductions - $latePenalty;
        
        // Ensure final amount is not negative
        $finalAmount = max(0, $finalAmount);

        // Determine if this is a manual calculation
        $isManual = (isset($data['total_hours']) || isset($data['bonus']) || isset($data['deductions']));

        return Payroll::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'period' => $month
            ],
            [
                'total_minutes' => $totalHours * 60, // Approximate if manual
                'total_hours' => $totalHours,
                'hourly_rate' => $hourlyRate,
                'base_salary' => $baseAmount,
                'total_amount' => $baseAmount, // Or maybe base_amount? Leaving as base for now.
                'bonus' => $bonus,
                'deductions' => $deductions,
                'late_count' => $lateCount,
                'late_penalty' => $latePenalty,
                'final_amount' => $finalAmount,
                'notes' => $notes,
                'status' => \App\Models\Payroll::STATUS_PENDING,
                'is_manual' => $isManual
            ]
        );
    }
}
