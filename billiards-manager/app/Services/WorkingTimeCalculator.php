<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\EmployeeShift;

class WorkingTimeCalculator
{
    /**
     * Calculate billable working time.
     * 
     * Rules:
     * 1. Billable Start = Max(Shift Start, Check In)
     *    EXCEPTION: If Late is APPROVED, Billable Start = Shift Start
     * 2. Billable End = Min(Shift End, Check Out)
     * 3. Duration = Billable End - Billable Start
     */
    public static function calculate(
        Attendance $attendance, 
        ?EmployeeShift $shift = null, 
        ?Carbon $forcedCheckoutTime = null
    ): array
    {
        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = $forcedCheckoutTime ? Carbon::parse($forcedCheckoutTime) : ($attendance->check_out ? Carbon::parse($attendance->check_out) : now());

        $result = [
            'total_minutes' => 0,
            'total_hours' => 0,
            'status' => $attendance->status ?? 'Present', // Preserve existing if set
            'early_minutes' => 0,
            'late_minutes' => 0,
            'payroll_start' => $checkIn, // Default to raw if no shift
            'payroll_end' => $checkOut,  // Default to raw if no shift
        ];

        // 1. Resolve Shift limits
        $shiftStart = null;
        $shiftEnd = null;

        if ($shift && $shift->shift) {
            // Reconstruct shift date/time
            $shiftDate = Carbon::parse($shift->shift_date);
            $shiftStart = Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $shift->shift->start_time);
            $shiftEnd = Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $shift->shift->end_time);

            if ($shiftEnd->lt($shiftStart)) {
                $shiftEnd->addDay();
            }
        } else {
             // Fallback: Use standard 8-17 or just use raw check-in/out
             // Logic without shift is tricky. We'll stick to raw.
        }

        if ($shiftStart && $shiftEnd) {
            
            // --- Determine Payroll Start ---
            $payrollStart = $checkIn->copy();
            
            // Core Logic: Billable starts at Shift Start if checked in earlier.
            if ($payrollStart->lt($shiftStart)) {
                $payrollStart = $shiftStart;
            }

            // Exception: If Late Reason Approved => Billable starts at Shift Start (System treats as if worked from start)
            // But verify: Does "Approved" mean "Forgiven Late Fee" only, or "Paid for missed time"?
            // Usually Approved Late = Paid from ARRIVAL, but No Fine.
            // BUT User requirement: "Tự động trừ 20k". "Duyệt -> Tính full".
            // Let's re-read user intent in previous context or just assume "Standard".
            // Requirement says: "Thời gian làm = thời điểm bắt đầu ca (hoặc check-in hợp lệ)".
            // "Checkout hộ phải dùng cùng công thức".
            // Let's stick to SAFE logic:
            // - If checked in Late 15m -> Paid for Actual (minus 15).
            // - Only if User requested "Approved Late = Paid for missed time" explicitly. 
            // - Code reads: `if ($attendance->approval_status === 'approved') { $payrollTimeStart = $shiftStart; }`
            // - So YES, existing code implies Approved Late = Paid from Shift Start. We will KEEP this.
            
            if ($attendance->approval_status === 'approved') {
                $payrollStart = $shiftStart;
            }

            // --- Determine Payroll End ---
            $payrollEnd = $checkOut->copy();
            
            // Core Logic: Billable ends at Shift End if checked out later.
            if ($payrollEnd->gt($shiftEnd)) {
                $payrollEnd = $shiftEnd;
            }

            // --- Calculation ---
            $minutes = 0;
            if ($payrollStart->lt($payrollEnd)) {
                $minutes = $payrollStart->diffInMinutes($payrollEnd);
            }
            
            $result['total_minutes'] = $minutes;
            $result['total_hours'] = round($minutes / 60, 2);
            $result['payroll_start'] = $payrollStart;
            $result['payroll_end'] = $payrollEnd;

            // --- Auxiliary Metrics ---
            
            // Early Minutes (Leaving before Shift End)
            if ($checkOut->lt($shiftEnd)) {
                $result['early_minutes'] = $checkOut->diffInMinutes($shiftEnd);
            }

            // Late Minutes (Arriving after Shift Start)
            // Note: This is "physical" late, regardless of approval
            if ($checkIn->gt($shiftStart)) {
                $result['late_minutes'] = $checkIn->diffInMinutes($shiftStart);
            }
        } else {
            // No Shift ? Raw calculation
            $minutes = $checkIn->diffInMinutes($checkOut);
            $result['total_minutes'] = $minutes;
            $result['total_hours'] = round($minutes / 60, 2);
        }

        return $result;
    }
}
