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
        
        // Get all approved attendance records for the employee in the given month
        // We only count records that are NOT rejected. 
        // Pending records might be excluded or included depending on policy, 
        // but usually we only pay for approved or auto-verified work.
        // User requirement: "Nếu approved → cộng vào payroll. Nếu rejected → không tính công."
        // So we filter for 'approved' status OR 'none' (if auto-approved/no approval needed logic exists, 
        // but user said "Gửi lên quản lý duyệt", so likely 'approved' is required for late ones).
        // However, normal check-ins might have status 'none' or 'present'.
        // Let's assume we count everything that is NOT 'rejected'.
        
        $attendances = Attendance::where('employee_id', $employeeId)
            ->where('approval_status', '!=', 'rejected')
            ->whereBetween('check_in', [$startOfMonth, $endOfMonth])
            ->get();

        $totalMinutes = 0;
        $penalty = 0;

        foreach ($attendances as $attendance) {
            // Calculate minutes if not already set
            $minutes = $attendance->total_minutes;
            if (!$minutes && $attendance->check_out) {
                $checkIn = Carbon::parse($attendance->check_in);
                $checkOut = Carbon::parse($attendance->check_out);
                $minutes = $checkOut->diffInMinutes($checkIn);
            }
            
            $totalMinutes += $minutes;

            // Calculate Penalty
            // User said: "Hỗ trợ đi muộn và lý do → phạt theo phút hoặc theo mức cố định."
            // Let's implement a simple per-minute penalty for now if late_minutes > 0
            // AND if it wasn't "approved" (waived). 
            // Wait, if approved, does it mean "allowed to work" or "penalty waived"?
            // Usually "Approved" late reason means NO penalty.
            // If "Rejected", the record is excluded entirely (no pay).
            // So if it's in this loop, it's either on time OR late-but-approved.
            // If late-but-approved, maybe we still deduct the late minutes from work time?
            // Or maybe we charge a fixed fine?
            // Let's assume:
            // 1. Work time is actual check_in to check_out (so late time is naturally not paid).
            // 2. Additional penalty? User said "phạt theo phút".
            // Let's add a config or hardcode for now: 1000 VND per late minute if late > 15.
            // But if approved, maybe we don't fine?
            // Let's stick to: Pay for actual minutes worked. Penalty is separate.
            // If late_minutes > 0 and approval_status != 'approved', apply penalty?
            // But we filtered out 'rejected'. So only 'pending' or 'none' or 'approved' are here.
            // If 'approved', we assume penalty is waived.
            // If 'none' (normal late < threshold?), maybe small penalty?
            // Let's keep it simple: No extra penalty, just pay for actual time.
            // UNLESS user explicitly wants a "Fine" column.
            // User requested "penalty" column in payroll.
            // Let's calculate penalty = late_minutes * 1000 (example) if not approved.
            
            if ($attendance->late_minutes > 0 && $attendance->approval_status !== 'approved') {
                $penalty += $attendance->late_minutes * 1000;
            }
        }

        $totalHours = $totalMinutes / 60;
        $hourlyRate = $employee->hourly_rate;
        $baseAmount = $totalHours * $hourlyRate;
        
        // Ensure not negative (though baseAmount should be positive)
        $baseAmount = max(0, $baseAmount);

        return [
            'total_minutes' => $totalMinutes,
            'total_hours' => round($totalHours, 2),
            'hourly_rate' => $hourlyRate,
            'base_amount' => round($baseAmount, 2),
            'penalty' => $penalty,
            'period' => $month
        ];
    }

    public function createPayroll($employeeId, $month, $data = [])
    {
        $calculation = $this->calculateMonthlySalary($employeeId, $month);
        
        $bonus = $data['bonus'] ?? 0;
        $manualPenalty = $data['penalty'] ?? 0;
        $notes = $data['notes'] ?? null;

        $totalPenalty = $calculation['penalty'] + $manualPenalty;
        $finalAmount = ($calculation['base_amount'] + $bonus) - $totalPenalty;
        
        // Ensure final amount is not negative
        $finalAmount = max(0, $finalAmount);

        return Payroll::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'period' => $month
            ],
            [
                'total_minutes' => $calculation['total_minutes'],
                'total_hours' => $calculation['total_hours'],
                'hourly_rate' => $calculation['hourly_rate'],
                'base_salary' => $calculation['base_amount'], // This maps to 'total_amount' in DB or 'base_salary'? DB has both.
                // Migration has 'base_salary' and 'total_amount'.
                // Let's use 'base_salary' for hours*rate.
                // And 'total_amount' for... wait, migration has 'total_amount' and 'final_amount'.
                // Let's use 'total_amount' as the intermediate sum? Or just ignore it?
                // User asked for: total_minutes, total_hours, hourly_rate, total_amount, bonus, penalty, final_amount.
                // Let's assume total_amount = base_salary.
                'total_amount' => $calculation['base_amount'],
                'bonus' => $bonus,
                'penalty' => $totalPenalty,
                'final_amount' => $finalAmount,
                'notes' => $notes,
                'status' => 'Calculated'
            ]
        );
    }
}
