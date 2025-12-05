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

        $employee->invalidateQrToken();

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

        $employee->invalidateQrToken();

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
        
        // Calculate total minutes
        $checkIn = Carbon::parse($attendance->check_in);
        $attendance->total_minutes = $checkIn->diffInMinutes($now);

        // Calculate early minutes (assume work ends at 17:00)
        $workEnd = Carbon::parse(today()->format('Y-m-d') . ' 17:00:00');
        if ($now->lt($workEnd)) {
            $attendance->early_minutes = $workEnd->diffInMinutes($now);
        }

        $attendance->save();
        $employee->invalidateQrToken();

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

        // Get active employees (checked in today)
        $activeEmployees = Attendance::with('employee')
            ->whereDate('check_in', today())
            ->orderBy('check_in', 'desc')
            ->get();

        return view('admin.attendance.monitor', compact('pendingLate', 'activeEmployees'));
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
        $minutes = $now->diffInMinutes($checkIn);

        $attendance->update([
            'check_out' => $now,
            'total_minutes' => $minutes,
            'status' => 'present',
            'admin_checkout_by' => Auth::id(),
            'admin_checkout_reason' => $request->reason
        ]);

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
}
