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
        $attendance->check_out = $now;
        
        // Find the shift to determine end time
        $employeeShift = \App\Models\EmployeeShift::where('employee_id', $employee->id)
            ->whereDate('shift_date', today())
            ->with('shift')
            ->first();

        // Calculate total minutes for PAYROLL (capped at shift end)
        $checkIn = Carbon::parse($attendance->check_in);
        $payrollEndTime = $now->copy(); // Default to now

        if ($employeeShift && $employeeShift->shift) {
            $shiftEndTime = Carbon::parse(today()->format('Y-m-d') . ' ' . $employeeShift->shift->end_time);
            
            // If checked out AFTER shift end, cap at shift end
            if ($now->gt($shiftEndTime)) {
                $payrollEndTime = $shiftEndTime;
            }
        }

        // Calculate duration in minutes (ensure non-negative)
        $workMinutes = $checkIn->diffInMinutes($payrollEndTime, false); // false = absolute difference if negative? No, false = relative.
        // Actually diffInMinutes return absolute by default unless false passed? 
        // Let's use max(0, ...)
        $attendance->total_minutes = max(0, $checkIn->diffInMinutes($payrollEndTime));

        // Calculate early minutes (assume work ends at 17:00 or Shift End)
        // Use Shift End if available, else 17:00 fallback
        $workEnd = $employeeShift && $employeeShift->shift 
            ? Carbon::parse(today()->format('Y-m-d') . ' ' . $employeeShift->shift->end_time)
            : Carbon::parse(today()->format('Y-m-d') . ' 17:00:00');

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
            'message' => 'Check-out thành công! Tổng thời gian: ' . round($attendance->total_minutes / 60, 2) . ' giờ.',
            'data' => $attendance
        ]);
    }

    public function approveLate($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update([
            'approval_status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        return response()->json(['status' => 'success', 'message' => 'Đã duyệt đi muộn.']);
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

        $now = now();
        $checkIn = \Carbon\Carbon::parse($attendance->check_in);

        if ($now->lt($checkIn)) {
             return response()->json(['status' => 'error', 'message' => 'Lỗi: Thời gian check-out (' . $now->format('H:i') . ') sớm hơn thời gian check-in (' . $checkIn->format('H:i') . '). Vui lòng kiểm tra lại thời gian server.'], 400);
        }

        $minutes = $now->diffInMinutes($checkIn);
        
        $attendance->fill([
            'check_out' => $now,
            'total_minutes' => $minutes,
            // 'status' => 'present', // Don't override status (keep Late if Late)
            'admin_checkout_by' => Auth::id(),
            'admin_checkout_reason' => $request->reason
        ]);

        // Calculate early minutes (assume work ends at 17:00, or should use Shift logic)
        // Ideally we should get the shift from EmployeeShift
        $workEnd = Carbon::parse($now->format('Y-m-d') . ' 17:00:00'); 
        if ($now->lt($workEnd)) {
             $attendance->early_minutes = $workEnd->diffInMinutes($now);
        }
        
        $attendance->save();
        
        $this->updateShiftStatus($attendance->employee_id, 'completed');

        \App\Models\ActivityLog::log('admin_checkout', "Admin checked out for employee ID {$attendance->employee_id}.", ['attendance_id' => $attendance->id, 'reason' => $request->reason, 'admin_id' => Auth::id()]);

        return response()->json(['status' => 'success', 'message' => 'Đã check-out hộ nhân viên thành công.']);
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
