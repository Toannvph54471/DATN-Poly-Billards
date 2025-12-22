<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\WorkingTimeCalculator;

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

        // 1. Kiểm tra chấm công hiện tại & Tự động đóng các phiên treo (Smart Check-in)
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('check_in', today())
            ->whereNull('check_out')
            ->first();

        if ($existingAttendance) {
            // Tìm ca làm việc ứng với lần check-in cũ
            $allShifts = \App\Models\EmployeeShift::where('employee_id', $employee->id)
                ->whereDate('shift_date', today())
                ->with('shift')
                ->get();
            
            $oldShift = null;
            $checkInTime = Carbon::parse($existingAttendance->check_in);
            
            // Tìm ca phù hợp nhất với giờ check-in cũ
            foreach ($allShifts as $s) {
                 if (!$s->shift) continue;
                 $start = Carbon::parse(today()->format('Y-m-d') . ' ' . $s->shift->start_time);
                 $end = Carbon::parse(today()->format('Y-m-d') . ' ' . $s->shift->end_time);
                 if ($end->lt($start)) $end->addDay();
                 
                 // Nếu check-in nằm trong khoảng [Giờ vào - 30p, Giờ ra]
                 if ($checkInTime->between($start->copy()->subMinutes(30), $end)) {
                     $oldShift = $s;
                     break;
                 }
            }
            // Nếu không tìm thấy, lấy ca đầu tiên làm mặc định
            if (!$oldShift && $allShifts->isNotEmpty()) $oldShift = $allShifts->first();
            
            $shouldAutoClose = false;
            $closeTime = now();
            
            if ($oldShift && $oldShift->shift) {
                 $end = Carbon::parse(today()->format('Y-m-d') . ' ' . $oldShift->shift->end_time);
                 if ($end->lt($oldShift->shift->start_time)) $end->addDay();
                 
                 // Logic: Nếu hiện tại đã quá giờ kết thúc ca 30 phút -> Coi như quên Check-out
                 if (now()->gt($end->copy()->addMinutes(30))) {
                     $shouldAutoClose = true;
                     $closeTime = $end; // Đóng phiên tại thời điểm kết thúc ca (tránh trả thừa lương)
                 }
            } else {
                 // Không có ca Đóng nếu đã treo quá 12 tiếng
                 if (now()->diffInHours($checkInTime) > 12) {
                     $shouldAutoClose = true;
                 }
            }
            
            if ($shouldAutoClose) {
                // Tính toán lương cho phiên cũ trước khi đóng
                $calc = WorkingTimeCalculator::calculate($existingAttendance, $oldShift, $closeTime);
                $existingAttendance->update([
                    'check_out' => $closeTime,
                    'total_minutes' => $calc['total_minutes'],
                    'early_minutes' => $calc['early_minutes'],
                    'status' => 'Present',
                    'notes' => trim($existingAttendance->notes . ' [Tự động Check-out: Quên quét mã]')
                ]);
                $this->updateShiftStatus($employee->id, 'completed');
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn đã check-in rồi (Ca hiện tại chưa kết thúc).',
                ], 400);
            }
        }

        // 2. Chọn Ca làm việc cho lần Check-in MỚI này
        // Tìm ca phù hợp nhất với giờ hiện tại (thay vì luôn lấy ca đầu tiên)
        $todayShifts = \App\Models\EmployeeShift::where('employee_id', $employee->id)
            ->whereDate('shift_date', today())
            ->with('shift')
            ->get();

        $employeeShift = null;
        $now = now();
        
        // Ưu tiên 1: Ca đang diễn ra hoặc sắp bắt đầu (Trong khoảng [Start - 15p, End])
        foreach ($todayShifts as $s) {
            if (!$s->shift) continue;
            $start = Carbon::parse(today()->format('Y-m-d') . ' ' . $s->shift->start_time);
            $end = Carbon::parse(today()->format('Y-m-d') . ' ' . $s->shift->end_time);
            if ($end->lt($start)) $end->addDay();
            
            if ($now->between($start->copy()->subMinutes(15), $end)) {
                $employeeShift = $s;
                break;
            }
        }
        
        // Ưu tiên 2: Nếu không thấy, tìm ca sắp tới gần nhất
        if (!$employeeShift) {
             $employeeShift = $todayShifts->filter(function($s) use ($now) {
                 if (!$s->shift) return false;
                 $start = Carbon::parse(today()->format('Y-m-d') . ' ' . $s->shift->start_time);
                 return $start->gt($now);
             })->sortBy('shift.start_time')->first();
             
             // Nếu vẫn không có, lấy ca vừa kết thúc gần nhất (để báo lỗi "Hết giờ")
             if (!$employeeShift) {
                  $employeeShift = $todayShifts->sortByDesc('shift.start_time')->first();
             }
        }

        if (!$employeeShift) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không có ca làm việc hôm nay.',
            ], 403);
        }

        // Kiểm tra đi muộn (Late)
        // Tính lại mốc thời gian dựa trên ca đã chọn
        $shiftStartTime = Carbon::parse(today()->format('Y-m-d') . ' ' . $employeeShift->shift->start_time);
        $shiftEndTime = Carbon::parse(today()->format('Y-m-d') . ' ' . $employeeShift->shift->end_time);

        if ($shiftEndTime->lt($shiftStartTime)) {
            $shiftEndTime->addDay();
        }

        // Validate 1: Kiểm tra nếu ca đã kết thúc
        if ($now->gt($shiftEndTime)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ca làm việc đã kết thúc (' . $employeeShift->shift->end_time . '). Bạn không thể check-in.',
            ], 403);
        }

        // Validate 2: Kiểm tra nếu đến quá sớm (> 15 phút)
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

        // Tính số phút đi muộn (nếu có, nhưng < ngưỡng 15p)
        $lateMinutes = 0;
        if ($now->gt($shiftStartTime)) {
            $lateMinutes = $shiftStartTime->diffInMinutes($now);
        }

        // Tạo bản ghi Check-in (Attendance)
        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'check_in' => $now,
            'status' => $lateMinutes > 0 ? 'Late' : 'Present',
            'late_minutes' => $lateMinutes,
            'approval_status' => 'none' // Không cần duyệt nếu đúng giờ hoặc muộn nhẹ
        ]);

        $this->updateShiftStatus($employee->id, 'active');

        $employee->invalidateQrToken();

        \App\Models\ActivityLog::log('check_in', "Employee {$employee->name} checked in.", ['attendance_id' => $attendance->id, 'time' => $now]);

        return response()->json([
            'status' => 'success',
            'message' => "Check-in thành công! Xin chào {$employee->name}.",
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
        
        // Tìm ca làm việc ứng với Ngày Check-in (để xử lý trường hợp ca qua đêm nếu cần)
        $employeeShift = \App\Models\EmployeeShift::where('employee_id', $employee->id)
            ->whereDate('shift_date', $attendance->check_in) 
            ->with('shift')
            ->first();

        // Tính toán giờ công bằng Service chung
        $calculation = WorkingTimeCalculator::calculate($attendance, $employeeShift);
        
        // Lưu thời gian Check-out thực tế
        $attendance->check_out = now();
        $attendance->total_minutes = $calculation['total_minutes'];
        $attendance->early_minutes = $calculation['early_minutes'];
        // Lưu ý: late_minutes đã được tính lúc Check-in nên không ghi đè ở đây để giữ nguyên trạng thái gốc.

        $attendance->save();
        $this->updateShiftStatus($employee->id, 'completed');
        $employee->invalidateQrToken();

        \App\Models\ActivityLog::log('check_out', "Employee {$employee->name} checked out.", ['attendance_id' => $attendance->id, 'time' => $now, 'total_hours' => round($attendance->total_minutes / 60, 2)]);

        return response()->json([
            'status' => 'success',
            'message' => "Check-out thành công! Tạm biệt {$employee->name}. Tổng thời gian: " . round($attendance->total_minutes / 60, 2) . ' giờ.',
            'data' => $attendance
        ]);
    }

    public function approveLate($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        // 1. Duyệt
        $attendance->approval_status = 'approved';
        $attendance->approved_by = Auth::id();
        $attendance->approved_at = now();
        $attendance->save(); // Lưu trạng thái trước

        // 2. Tính lại lương (nếu đã check-out)
        // Nếu đã check-out, cần tính lại total_minutes vì giờ Bắt Đầu có thể thay đổi (được tính từ đầu ca)
        
        if ($attendance->check_out) {
             $checkoutTime = Carbon::parse($attendance->check_out);
             // Tìm ca
             $employeeShift = \App\Models\EmployeeShift::where('employee_id', $attendance->employee_id)
                ->whereDate('shift_date', $attendance->check_in)
                ->with('shift')
                ->first();
                
             if ($employeeShift && $employeeShift->shift) {
                 // Tính lại bằng service
                 $calculation = WorkingTimeCalculator::calculate($attendance, $employeeShift);
                 
                 $attendance->total_minutes = $calculation['total_minutes'];
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
        // Lấy danh sách xin đi muộn đang chờ duyệt
        $pendingLate = Attendance::with('employee')
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Lấy TẤT CẢ ca làm việc hôm nay
        $todayShifts = \App\Models\EmployeeShift::whereDate('shift_date', today())
            ->with(['employee', 'shift'])
            ->get();
            
        // Pre-fetch dữ liệu chấm công hôm nay để tránh lỗi N+1
        $attendances = Attendance::whereDate('check_in', today())
            ->get()
            ->keyBy('employee_id');

        foreach ($todayShifts as $shift) {
            $att = $attendances->get($shift->employee_id);
            $shift->attendance = $att; // Gán thủ công để View hiển thị
            
            if ($att && !$att->check_out) {
                 // Tính thời gian làm việc thực tế (Live)
                 $calc = WorkingTimeCalculator::calculate($att, $shift, now());
                 $shift->live_billable_minutes = $calc['total_minutes'];
            }
        }

        return view('admin.attendance.monitor', compact('pendingLate', 'todayShifts'));
    }
    
    // Helper lấy token cho máy giả lập
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
        
        // Tạo token mới nếu chưa có hoặc đã hết hạn
        if (!$employee->qr_token || $employee->qr_token_expires_at < now()) {
            $employee->generateQrToken();
        }

        return view('attendance.my-qr', compact('employee'));
    }

    public function adminCheckout(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'checkout_time' => 'nullable|date'
        ]);

        $attendance = Attendance::findOrFail($id);
        
        if ($attendance->check_out) {
            return response()->json(['status' => 'error', 'message' => 'Nhân viên này đã check-out rồi.']);
        }

        // Ưu tiên dùng thời gian Admin nhập, nếu không thì dùng thời gian hiện tại
        $checkoutTime = $request->checkout_time ? Carbon::parse($request->checkout_time) : now();
        $checkInTime = \Carbon\Carbon::parse($attendance->check_in);

        // Validation: Thời gian check-out không thể trước check-in
        if ($checkoutTime->lt($checkInTime)) {
             return response()->json(['status' => 'error', 'message' => 'Thời gian check-out không thể trước thời gian check-in (' . $checkInTime->format('H:i d/m/Y') . ').']);
        }

        // Tìm ca làm việc
        $employeeShift = \App\Models\EmployeeShift::where('employee_id', $attendance->employee_id)
            ->whereDate('shift_date', $checkInTime) // Khớp ngày check-in
            ->with('shift')
            ->first();

        // Tính toán lại giờ công
        $calculation = WorkingTimeCalculator::calculate($attendance, $employeeShift, $checkoutTime);
        $minutes = $calculation['total_minutes'];
        $earlyMinutes = $calculation['early_minutes'];

        $attendance->fill([
            'check_out' => $checkoutTime,
            'total_minutes' => $minutes,
            'early_minutes' => $earlyMinutes,
            'admin_checkout_by' => Auth::id(),
            'admin_checkout_reason' => $request->reason
        ]);
        
        $attendance->save();
        
        $this->updateShiftStatus($attendance->employee_id, 'completed');

        \App\Models\ActivityLog::log('admin_checkout', "Admin checked out for employee ID {$attendance->employee_id} at {$checkoutTime}.", ['attendance_id' => $attendance->id, 'reason' => $request->reason, 'admin_id' => Auth::id()]);

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
