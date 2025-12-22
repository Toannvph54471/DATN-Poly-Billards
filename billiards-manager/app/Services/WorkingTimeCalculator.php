<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\EmployeeShift;

class WorkingTimeCalculator
{
    /**
     * Tính toán thời gian làm việc có lương (Billable Time).
     * 
     * Quy tắc:
     * 1. Bắt đầu tính lương = Max(Giờ Vào Ca, Giờ Check-in Thực tế)
     *    NGOẠI LỆ: Nếu Đi muộn được DUYỆT (Approved) -> Bắt đầu tính từ Giờ Vào Ca (coi như đúng giờ).
     * 2. Kết thúc tính lương = Min(Giờ Kết Thúc Ca, Giờ Check-out Thực tế)
     * 3. Tổng thời gian = Kết thúc - Bắt đầu
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
            'status' => $attendance->status ?? 'Present', // Giữ nguyên trạng thái cũ
            'early_minutes' => 0,
            'late_minutes' => 0,
            'payroll_start' => $checkIn, // Mặc định là Check-in thực nếu không có ca
            'payroll_end' => $checkOut,  // Mặc định là Check-out thực nếu không có ca
        ];

        // 1. Phân giải giới hạn Ca làm việc
        $shiftStart = null;
        $shiftEnd = null;

        if ($shift && $shift->shift) {
            // Tái tạo thời gian ca làm việc từ ngày phân công
            $shiftDate = Carbon::parse($shift->shift_date);
            $shiftStart = Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $shift->shift->start_time);
            $shiftEnd = Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $shift->shift->end_time);

            if ($shiftEnd->lt($shiftStart)) {
                $shiftEnd->addDay();
            }
        }

        if ($shiftStart && $shiftEnd) {
            
            // --- Xác định Thời điểm Bắt đầu tính lương ---
            $payrollStart = $checkIn->copy();
            
            // Logic Cốt lõi: Lương bắt đầu từ Giờ Vào Ca (nếu đến sớm hơn).
            if ($payrollStart->lt($shiftStart)) {
                $payrollStart = $shiftStart;
            }

            // Ngoại lệ: Nếu lý do đi muộn đã được DUYỆT -> Tính lương từ đầu ca (như check-in đúng giờ)
            if ($attendance->approval_status === 'approved') {
                $payrollStart = $shiftStart;
            }

            // --- Xác định Thời điểm Kết thúc tính lương ---
            $payrollEnd = $checkOut->copy();
            
            // Logic Cốt lõi: Lương kết thúc tại Giờ Kết Thúc Ca (nếu về muộn hơn/OT không phép).
            if ($payrollEnd->gt($shiftEnd)) {
                $payrollEnd = $shiftEnd;
            }

            // --- Tính toán thời lượng ---
            $minutes = 0;
            if ($payrollStart->lt($payrollEnd)) {
                $minutes = $payrollStart->diffInMinutes($payrollEnd);
            }
            
            $result['total_minutes'] = $minutes;
            $result['total_hours'] = round($minutes / 60, 2);
            $result['payroll_start'] = $payrollStart;
            $result['payroll_end'] = $payrollEnd;

            // --- Các chỉ số phụ ---
            
            // Phút về sớm (Check-out trước Giờ Kết Thúc Ca)
            if ($checkOut->lt($shiftEnd)) {
                $result['early_minutes'] = $checkOut->diffInMinutes($shiftEnd);
            }

            // Phút đi muộn (Check-in sau Giờ Vào Ca)
            // Lưu ý: Tính theo giờ thực tế, bất kể có được duyệt hay không
            if ($checkIn->gt($shiftStart)) {
                $result['late_minutes'] = $checkIn->diffInMinutes($shiftStart);
            }
        } else {
            // Trường hợp Không có Ca: Tính theo giờ thực tế check-in/out
            $minutes = $checkIn->diffInMinutes($checkOut);
            $result['total_minutes'] = $minutes;
            $result['total_hours'] = round($minutes / 60, 2);
        }

        return $result;
    }
}
