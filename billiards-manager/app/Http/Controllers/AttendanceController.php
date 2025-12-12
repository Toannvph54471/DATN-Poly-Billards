<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Standard work hours (could be moved to config/database)
    const WORK_START_TIME = '08:00:00';
    const LATE_THRESHOLD_MINUTES = 15;

    private function updateShiftStatus($employeeId, $status)
    {
        $shift = \App\Models\EmployeeShift::where('employee_id', $employeeId)
            ->whereDate('shift_date', today())
            ->first();

        if ($shift) {
            if ($status === 'active') {
                $shift->update([
                    'status' => \App\Models\EmployeeShift::STATUS_ACTIVE,
                    'actual_start_time' => now(),
                    'is_locked' => true
                ]);
            } elseif ($status === 'completed') {
                $shift->update([
                    'status' => \App\Models\EmployeeShift::STATUS_COMPLETED,
                    'actual_end_time' => now()
                ]);
            }
        } else {
             // Fallback: Try to find any active shift for this employee
             $shift = \App\Models\EmployeeShift::where('employee_id', $employeeId)
                ->where('status', \App\Models\EmployeeShift::STATUS_ACTIVE)
                ->first();
             
             if ($shift && $status === 'completed') {
                  $shift->checkOut();
             }
        }
    }
    public function checkIn(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
        ]);

        $employee = Employee::where('qr_token', $request->qr_token)
            ->where('qr_token_expires_at', '>', now())
            ->first();

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mã QR không hợp lệ hoặc đã hết hạn.',
            ], 400);
        }

        // Check if already checked in today
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('check_in', today())
            ->whereNull('check_out')
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn đã check-in rồi.',
            ], 400);
        }

        // Check for assigned shift today
        $employeeShift = \App\Models\EmployeeShift::where('employee_id', $employee->id)
            ->whereDate('shift_date', today())
            ->with('shift')
            ->first();

        if (!$employeeShift) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không có ca làm việc hôm nay.',
            ], 403);
        }

        // Check for Late
        $now = now();
        // Use shift start time from the assigned shift
        $shiftStartTime = Carbon::parse(today()->format('Y-m-d') . ' ' . $employeeShift->shift->start_time);
        $shiftEndTime = Carbon::parse(today()->format('Y-m-d') . ' ' . $employeeShift->shift->end_time);

        if ($shiftEndTime->lt($shiftStartTime)) {
            $shiftEndTime->addDay();
        }

        // Validate 1: Check if Shift Ended
        if ($now->gt($shiftEndTime)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ca làm việc đã kết thúc (' . $employeeShift->shift->end_time . '). Bạn không thể check-in.',
            ], 403);
        }

        // Validate 2: Check if Too Early (> 15 minutes)
        $earlyLimit = $shiftStartTime->copy()->subMinutes(15);
        if ($now->lt($earlyLimit)) {
             return response()->json([
                'status' => 'error',
                'message' => 'Bạn chỉ có thể check-in trước giờ vào ca 15 phút. Giờ vào ca: ' . $employeeShift->shift->start_time,
            ], 403);
        }

        $lateThreshold = $shiftStartTime->copy()->addMinutes(self::LATE_THRESHOLD_MINUTES);

        if ($now->gt($lateThreshold)) {
            return response()->json([
                'status' => 'LATE_REASON_REQUIRED',
                'message' => 'Bạn đi muộn quá 15 phút so với giờ vào ca (' . $employeeShift->shift->start_time . '). Vui lòng nhập lý do.',
                'employee_id' => $employee->id
            ], 403);
        }

        // Calculate late minutes (if any, but < threshold)
        $lateMinutes = 0;
        if ($now->gt($shiftStartTime)) {
            $lateMinutes = $shiftStartTime->diffInMinutes($now);
        }

        // Create Attendance
        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'check_in' => $now,
            'status' => $lateMinutes > 0 ? 'Late' : 'Present',
            'late_minutes' => $lateMinutes,
            'approval_status' => 'none' // No approval needed if on time or within threshold
        ]);

        $this->updateShiftStatus($employee->id, 'active');

        $employee->invalidateQrToken();

        \App\Models\ActivityLog::log('check_in', "Employee {$employee->name} checked in.", ['attendance_id' => $attendance->id, 'time' => $now]);

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in thành công!',
            'data' => $attendance
        ]);
    }

    public function submitLateReason(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
            'reason' => 'required|string|max:255'
        ]);

        $employee = Employee::where('qr_token', $request->qr_token)
            ->where('qr_token_expires_at', '>', now())
            ->first();

        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Mã QR không hợp lệ.'], 400);
        }

        $employeeShift = \App\Models\EmployeeShift::where('employee_id', $employee->id)
            ->whereDate('shift_date', today())
            ->with('shift')
            ->first();

        if (!$employeeShift) {
             return response()->json(['status' => 'error', 'message' => 'Không tìm thấy ca làm việc.'], 400);
        }

        $now = now();
        $shiftStartTime = Carbon::parse(today()->format('Y-m-d') . ' ' . $employeeShift->shift->start_time);
        $lateMinutes = $shiftStartTime->diffInMinutes($now);

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'check_in' => $now,
            'status' => 'Late',
            'late_minutes' => $lateMinutes,
            'late_reason' => $request->reason,
            'approval_status' => 'pending'
        ]);

        $this->updateShiftStatus($employee->id, 'active');

        $employee->invalidateQrToken();

        \App\Models\ActivityLog::log('check_in_late', "Employee {$employee->name} submitted late reason.", ['attendance_id' => $attendance->id, 'reason' => $request->reason, 'late_minutes' => $lateMinutes]);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã gửi lý do đi muộn. Vui lòng đợi quản lý duyệt.',
            'data' => $attendance
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
        ]);

        $employee = Employee::where('qr_token', $request->qr_token)
            ->where('qr_token_expires_at', '>', now())
            ->first();

        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Mã QR không hợp lệ.'], 400);
        }

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('check_in', today())
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            return response()->json(['status' => 'error', 'message' => 'Bạn chưa check-in.'], 400);
        }

        $now = now();
        
        // Find the shift based on Check-in Date (to handle crossover days correctly if needed)
        // Note: Using today() for check_in date as this is "live" checkout.
        $employeeShift = \App\Models\EmployeeShift::where('employee_id', $employee->id)
            ->whereDate('shift_date', $attendance->check_in) 
            ->with('shift')
            ->first();

        $payrollTimeStart = Carbon::parse($attendance->check_in);
        $payrollTimeEnd = $now->copy();
        
        $shiftEndTime = null;

        if ($employeeShift && $employeeShift->shift) {
            $shiftDate = Carbon::parse($employeeShift->shift_date);
            $shiftStart = Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $employeeShift->shift->start_time);
            $shiftEnd = Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $employeeShift->shift->end_time);
            
            if ($shiftEnd->lt($shiftStart)) {
                $shiftEnd->addDay();
            }
            $shiftEndTime = $shiftEnd; // Save for early calculation

            // Rule 1: Determine Payroll Start
            // If Late reason is Approved, count from Shift Start
            if ($attendance->approval_status === 'approved') {
                 $payrollTimeStart = $shiftStart;
            } else {
                 // Otherwise, Cap Start at Shift Start (Early Check-in doesn't count, Late counts as late)
                 if ($payrollTimeStart->lt($shiftStart)) {
                      $payrollTimeStart = $shiftStart;
                 }
            }
            
            // Rule 2: Cap End at Shift End (Late Checkout doesn't count)
            if ($payrollTimeEnd->gt($shiftEnd)) {
                $payrollTimeEnd = $shiftEnd;
            }
        }

        // Calculate minutes (ensure non-negative)
        // If start > end (e.g. checked in after shift ended?), minutes = 0
        if ($payrollTimeStart->gt($payrollTimeEnd)) {
            $attendance->total_minutes = 0;
        } else {
            $attendance->total_minutes = $payrollTimeStart->diffInMinutes($payrollTimeEnd);
        }
        
        // Save actual checkout time
        $attendance->check_out = $now;

        // Calculate early minutes
        // Use Shift End if available, else 17:00 fallback
        $workEnd = $shiftEndTime ?: Carbon::parse(today()->format('Y-m-d') . ' 17:00:00');

        if ($now->lt($workEnd)) {
            $attendance->early_minutes = $workEnd->diffInMinutes($now);
        } else {
             $attendance->early_minutes = 0;
        }

        $attendance->save();
        $this->updateShiftStatus($employee->id, 'completed');
        $employee->invalidateQrToken();

        \App\Models\ActivityLog::log('check_out', "Employee {$employee->name} checked out.", ['attendance_id' => $attendance->id, 'time' => $now, 'total_hours' => round($attendance->total_minutes / 60, 2)]);

        return response()->json([
            'status' => 'success',
            'message' => 'Check-out thành công! Tổng thời gian tính lương: ' . round($attendance->total_minutes / 60, 2) . ' giờ.',
            'data' => $attendance
        ]);
    }

    public function approveLate($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        // 1. Approve
        $attendance->approval_status = 'approved';
        $attendance->approved_by = Auth::id();
        $attendance->approved_at = now();
        $attendance->save(); // Save status first

        // 2. Recalculate Payroll if checked out OR if checked in (future checkout will handle it, but for Monitor we might want to know?)
        // Currently monitor doesn't show salary until checkout.
        // However, if they ARE checked out, we must recalculate.
        
        if ($attendance->check_out) {
             $checkoutTime = Carbon::parse($attendance->check_out);
             // Find Shift
             $employeeShift = \App\Models\EmployeeShift::where('employee_id', $attendance->employee_id)
                ->whereDate('shift_date', $attendance->check_in)
                ->with('shift')
                ->first();
                
             if ($employeeShift && $employeeShift->shift) {
                $shiftDate = Carbon::parse($employeeShift->shift_date);
                $shiftStart = Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $employeeShift->shift->start_time);
                $shiftEnd = Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $employeeShift->shift->end_time);

                if ($shiftEnd->lt($shiftStart)) {
                    $shiftEnd->addDay();
                }
                
                // If Approved, Start = Shift Start
                $payrollTimeStart = $shiftStart;
                
                // End = min(Checkout, Shift End)
                $payrollTimeEnd = $checkoutTime->copy();
                if ($payrollTimeEnd->gt($shiftEnd)) {
                    $payrollTimeEnd = $shiftEnd;
                }
                
                $minutes = ($payrollTimeStart->gt($payrollTimeEnd)) ? 0 : $payrollTimeStart->diffInMinutes($payrollTimeEnd);
                
                $attendance->total_minutes = $minutes;
                $attendance->save();
             }
        }

        return response()->json(['status' => 'success', 'message' => 'Đã duyệt đi muộn. Lương sẽ được tính từ đầu ca.']);
    }

    public function rejectLate($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update([
            'approval_status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        return response()->json(['status' => 'success', 'message' => 'Đã từ chối đi muộn.']);
    }

    public function monitor()
    {
        // Get pending late requests
        $pendingLate = Attendance::with('employee')
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get ALL shifts for today (for full status monitoring)
        $todayShifts = \App\Models\EmployeeShift::whereDate('shift_date', today())
            ->with(['employee', 'shift'])
            ->get();
            
        // Append real-time status to each shift (optional, purely for explicit knowing)
        // Accessor $shift->real_time_status is available automatically when accessed.

        return view('admin.attendance.monitor', compact('pendingLate', 'todayShifts'));
    }
    
    // Helper for simulator to get token
    public function getTestToken($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        return response()->json([
            'status' => 'success',
            'token' => $employee->generateQrToken()
        ]);
    }
    
    public function simulator()
    {
        $employees = Employee::all();
        return view('attendance.simulator', compact('employees'));
    }
    public function myQr()
    {
        $user = Auth::user();
        if (!$user || !$user->employee) {
            return redirect()->route('home')->with('error', 'Bạn không phải là nhân viên.');
        }

        $employee = $user->employee;
        
        // Generate a new token if one doesn't exist or is expired
        if (!$employee->qr_token || $employee->qr_token_expires_at < now()) {
            $employee->generateQrToken();
        }

        return view('attendance.my-qr', compact('employee'));
    }

    public function adminCheckout(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $attendance = Attendance::findOrFail($id);
        
        if ($attendance->check_out) {
            return response()->json(['status' => 'error', 'message' => 'Nhân viên này đã check-out rồi.']);
        }

        $checkoutTime = now(); // Use Now as Checkout Time
        $checkInTime = \Carbon\Carbon::parse($attendance->check_in);

        // Find Shift
        $employeeShift = \App\Models\EmployeeShift::where('employee_id', $attendance->employee_id)
            ->whereDate('shift_date', $checkInTime) // Match Check-in date
            ->with('shift')
            ->first();

        $payrollTimeStart = $checkInTime->copy();
        $payrollTimeEnd = $checkoutTime->copy();
        $shiftEndTime = null;

        if ($employeeShift && $employeeShift->shift) {
            $shiftDate = Carbon::parse($employeeShift->shift_date);
            $shiftStart = Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $employeeShift->shift->start_time);
            $shiftEnd = Carbon::parse($shiftDate->format('Y-m-d') . ' ' . $employeeShift->shift->end_time);
            
            if ($shiftEnd->lt($shiftStart)) {
                $shiftEnd->addDay();
            }
            $shiftEndTime = $shiftEnd;

            // Rule 1: Determine Payroll Start
            if ($attendance->approval_status === 'approved') {
                 $payrollTimeStart = $shiftStart;
            } else {
                 // Cap Start at Shift Start
                 if ($payrollTimeStart->lt($shiftStart)) {
                      $payrollTimeStart = $shiftStart;
                 }
            }
            
            // Rule 2: Cap End at Shift End (Late Checkout doesn't count)
            if ($payrollTimeEnd->gt($shiftEnd)) {
                $payrollTimeEnd = $shiftEnd;
            }
        }
        
        // Calculate minutes (avoid negative)
        if ($payrollTimeStart->gt($payrollTimeEnd)) {
             $minutes = 0;
        } else {
             $minutes = $payrollTimeStart->diffInMinutes($payrollTimeEnd);
        }

        $attendance->fill([
            'check_out' => $checkoutTime,
            'total_minutes' => $minutes,
            'admin_checkout_by' => Auth::id(),
            'admin_checkout_reason' => $request->reason
        ]);

        // Calculate early minutes
        $workEnd = $shiftEndTime ?: Carbon::parse($attendance->check_in)->setTime(17, 0); 
        
        // Only calculate early minutes if checkout is BEFORE shift end
        if ($checkoutTime->lt($workEnd)) {
             $attendance->early_minutes = $checkoutTime->diffInMinutes($workEnd);
        } else {
             $attendance->early_minutes = 0;
        }
        
        $attendance->save();
        
        $this->updateShiftStatus($attendance->employee_id, 'completed');

        \App\Models\ActivityLog::log('admin_checkout', "Admin checked out for employee ID {$attendance->employee_id}.", ['attendance_id' => $attendance->id, 'reason' => $request->reason, 'admin_id' => Auth::id()]);

        return response()->json(['status' => 'success', 'message' => 'Đã check-out hộ nhân viên thành công. Giờ công: ' . round($minutes / 60, 2) . 'h']);
    }

    public function manualCheckoutHistory()
    {
        $history = Attendance::whereNotNull('admin_checkout_by')
            ->with(['employee', 'adminCheckoutUser'])
            ->latest('check_out')
            ->paginate(20);

        return view('admin.attendance.manual-checkout-history', compact('history'));
    }
    public function processScan(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
        ]);

        $employee = Employee::where('qr_token', $request->qr_token)
            ->where('qr_token_expires_at', '>', now())
            ->first();

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mã QR không hợp lệ hoặc đã hết hạn.',
            ], 400);
        }

        // Check if employee has an active check-in (checked in today, but not checked out)
        $activeAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('check_in', today())
            ->whereNull('check_out')
            ->first();

        if ($activeAttendance) {
            // Already checked in -> perform Check Out
            return $this->checkOut($request);
        } else {
            // Not checked in (or checked out already) -> perform Check In
            // Note: If checked out already, checkIn might return "already checked in" error if logic prevents multiple shifts.
            // Let's rely on checkIn's internal logic.
            return $this->checkIn($request);
        }
    }
}
