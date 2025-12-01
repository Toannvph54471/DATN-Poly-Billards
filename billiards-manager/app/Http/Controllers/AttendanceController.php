<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $request->validate([
            'employee_code' => 'required|exists:employees,employee_code',
            'manager_username' => 'nullable|string',
            'manager_password' => 'nullable|string',
        ]);

        $employee = Employee::where('employee_code', $request->employee_code)->firstOrFail();

        // 1. Check if already checked in (Active shift exists)
        $activeShift = EmployeeShift::where('employee_id', $employee->id)
            ->whereDate('shift_date', today())
            ->whereNotNull('actual_start_time')
            ->whereNull('actual_end_time')
            ->first();

        if ($activeShift) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nhân viên đã check-in trước đó và chưa check-out.',
            ], 400);
        }

        // 2. Find scheduled shift
        // We look for shifts assigned to Today OR Yesterday (to handle overnight shifts checked in after midnight)
        $now = now();
        
        $candidates = EmployeeShift::with('shift')
            ->where('employee_id', $employee->id)
            ->whereIn('status', ['Scheduled'])
            ->whereBetween('shift_date', [today()->subDay(), today()])
            ->get();

        $scheduledShift = $candidates->filter(function ($shift) use ($now) {
            // 1. Get raw time strings (H:i:s) - Now guaranteed to be strings
            $startTimeStr = $shift->shift->start_time;
            $endTimeStr = $shift->shift->end_time;

            // 2. Construct Carbon objects based on shift_date
            $shiftStart = Carbon::parse($shift->shift_date->format('Y-m-d') . ' ' . $startTimeStr);
            $shiftEnd = Carbon::parse($shift->shift_date->format('Y-m-d') . ' ' . $endTimeStr);

            // 3. Handle Overnight Shifts (e.g. 22:00 -> 06:00)
            if ($shiftEnd->lt($shiftStart)) {
                $shiftEnd->addDay();
            }

            // 4. Define Valid Check-in Window
            // Earliest: 30 mins before start
            $earliestCheckIn = $shiftStart->copy()->subMinutes(30);
            
            // We attach these calculated times to the object for later use
            $shift->calculated_start = $shiftStart;
            $shift->calculated_end = $shiftEnd;

            return $now->between($earliestCheckIn, $shiftEnd);
        })->sortBy(function($shift) use ($now) {
            return $now->diffInMinutes($shift->calculated_start);
        })->first();

        if (!$scheduledShift) {
            // Debug info for failure
            if ($candidates->isNotEmpty()) {
                 $debugInfo = $candidates->map(function($s) {
                    return [
                        'shift_id' => $s->shift_id,
                        'shift_date' => $s->shift_date->format('Y-m-d'),
                        'start_time' => $s->shift->start_time,
                        'end_time' => $s->shift->end_time,
                    ];
                });

                return response()->json([
                    'status' => 'error',
                    'message' => 'Chưa đến giờ vào ca hoặc đã quá giờ ca làm việc.',
                    'debug' => [
                        'server_time' => $now->format('Y-m-d H:i:s'),
                        'timezone' => config('app.timezone'),
                        'shifts_found' => $debugInfo
                    ]
                ], 400);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Nhân viên không có ca làm việc nào được xếp lịch vào lúc này.',
                'debug' => [
                    'server_time' => $now->format('Y-m-d H:i:s'),
                    'timezone' => config('app.timezone'),
                ]
            ], 400);
        }

        // 3. Validate Time (Late Check)
        // Use the calculated start time from the filter step
        $shiftStart = $scheduledShift->calculated_start;
        $lateThreshold = $shiftStart->copy()->addMinutes(15); // 15 minutes grace period

        if ($now->gt($lateThreshold)) {
            // LATE! Check for Manager Approval
            if (!$request->manager_username || !$request->manager_password) {
                return response()->json([
                    'status' => 'REQUIRE_MANAGER_APPROVAL',
                    'message' => 'Đi muộn quá 15 phút. Cần quản lý duyệt.',
                ], 403);
            }

            // Verify Manager Credentials
            if (!Auth::attempt(['email' => $request->manager_username, 'password' => $request->manager_password])) {
                 // Try username if email fails (if system uses username) - assuming email for now based on Laravel default
                 // Or check if user is actually a manager/admin
                 return response()->json([
                    'status' => 'error',
                    'message' => 'Thông tin quản lý không chính xác.',
                ], 401);
            }
            
            $user = Auth::user();
            if (!$user->hasRole('admin') && !$user->hasRole('manager')) { // Assuming Spatie roles or similar
                 return response()->json([
                    'status' => 'error',
                    'message' => 'Tài khoản này không có quyền duyệt đi muộn.',
                ], 403);
            }
            
            // Approved
            $scheduledShift->note = "Đi muộn (Duyệt bởi: " . $user->name . ")";
            $scheduledShift->confirmed_by = $user->id;
        }

        // 4. Perform Check-in
        unset($scheduledShift->calculated_start);
        unset($scheduledShift->calculated_end);
        
        $scheduledShift->checkIn();

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in thành công!',
            'data' => $scheduledShift
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'employee_code' => 'required|exists:employees,employee_code',
        ]);

        $employee = Employee::where('employee_code', $request->employee_code)->firstOrFail();

        // Find active shift
        $activeShift = EmployeeShift::where('employee_id', $employee->id)
            ->whereDate('shift_date', today())
            ->whereNotNull('actual_start_time')
            ->whereNull('actual_end_time')
            ->first();

        if (!$activeShift) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nhân viên chưa check-in hoặc đã check-out.',
            ], 400);
        }

        $activeShift->checkOut();

        return response()->json([
            'status' => 'success',
            'message' => 'Check-out thành công!',
            'data' => $activeShift
        ]);
    }
    public function monitor()
    {
        // Get all employees with their latest shift status for today
        $employees = Employee::where('status', 'Active')
            ->with(['shifts' => function ($query) {
                $query->whereDate('shift_date', today())
                      ->orderBy('created_at', 'desc');
            }])
            ->get()
            ->map(function ($employee) {
                $currentShift = $employee->shifts->first();
                
                $isOnline = false;
                $checkInTime = null;
                $duration = null;

                if ($currentShift && $currentShift->actual_start_time && !$currentShift->actual_end_time) {
                    $isOnline = true;
                    $checkInTime = $currentShift->actual_start_time;
                    $duration = $currentShift->actual_start_time->diffForHumans(null, true);
                }

                $employee->is_online = $isOnline;
                $employee->check_in_time = $checkInTime;
                $employee->work_duration = $duration;
                
                return $employee;
            });

        return view('admin.attendance.monitor', compact('employees'));
    }

    public function getActiveEmployees()
    {
        $activeEmployees = EmployeeShift::with('employee')
            ->whereDate('shift_date', today())
            ->whereNotNull('actual_start_time')
            ->whereNull('actual_end_time')
            ->get()
            ->map(function ($shift) {
                return [
                    'name' => $shift->employee->name,
                    'position' => $shift->employee->position,
                    'start_time' => $shift->actual_start_time->format('H:i'),
                    'duration' => $shift->actual_start_time->diffForHumans(null, true)
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $activeEmployees
        ]);
    }
    // --- Dynamic QR Code Section ---

    // --- Individual Dynamic QR Code Section ---

    public function myQr()
    {
        return view('attendance.my_qr');
    }

    public function publicScan()
    {
        return view('attendance.public_scan');
    }

    public function simulator()
    {
        // Get all employees for the simulator dropdown
        $employees = Employee::all();
        return view('attendance.simulator', compact('employees'));
    }

    public function getTestToken($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $token = $employee->generateQrToken();
        
        return response()->json([
            'status' => 'success',
            'token' => $token,
            'employee' => $employee->name
        ]);
    }

    public function getMyQrToken()
    {
        try {
            \Log::info('getMyQrToken called');
            
            $user = Auth::user();
            \Log::info('User ID: ' . ($user ? $user->id : 'null'));
            
            if (!$user) {
                \Log::error('No authenticated user');
                return response()->json(['status' => 'error', 'message' => 'Chưa đăng nhập'], 401);
            }
            
            if (!$user->employee) {
                \Log::error('User has no employee record', ['user_id' => $user->id]);
                return response()->json(['status' => 'error', 'message' => 'Không tìm thấy thông tin nhân viên'], 404);
            }

            $employee = $user->employee;
            \Log::info('Employee found', ['employee_id' => $employee->id]);

            // Check if current token is valid for at least 30 more seconds
            if ($employee->qr_token && $employee->qr_token_expires_at && $employee->qr_token_expires_at->gt(now()->addSeconds(30))) {
                \Log::info('Returning existing token');
                return response()->json([
                    'token' => $employee->qr_token,
                    'expires_in' => $employee->qr_token_expires_at->timestamp - now()->timestamp
                ]);
            }

            // Generate new token
            \Log::info('Generating new token');
            $token = $employee->generateQrToken();
            \Log::info('Token generated successfully', ['token_length' => strlen($token)]);

            return response()->json([
                'token' => $token,
                'expires_in' => 120 // 2 minutes
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception in getMyQrToken', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
        ]);

        // 1. Find Employee by Token
        $employee = Employee::where('qr_token', $request->qr_token)
            ->where('qr_token_expires_at', '>', now())
            ->first();

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mã QR không hợp lệ hoặc đã hết hạn.',
            ], 400);
        }

        // 2. Check if already checked in (Active shift exists)
        $activeShift = EmployeeShift::where('employee_id', $employee->id)
            ->whereDate('shift_date', today())
            ->whereNotNull('actual_start_time')
            ->whereNull('actual_end_time')
            ->first();

        // If already checked in -> Check OUT?
        // For simplicity, let's assume this scanner is for Check-IN only or toggles.
        // Let's implement Toggle: If checked in -> Check Out. If not -> Check In.
        
        if ($activeShift) {
            // Perform Check-out
            $activeShift->checkOut();
            
            // Invalidate Token (One-time use)
            $employee->invalidateQrToken();

            return response()->json([
                'status' => 'success',
                'message' => 'Check-out thành công! Hẹn gặp lại ' . $employee->name,
                'type' => 'checkout',
                'employee' => $employee->name
            ]);
        }

        // 3. Find scheduled shift for Check-in
        $now = now();
        $candidates = EmployeeShift::with('shift')
            ->where('employee_id', $employee->id)
            ->whereIn('status', ['Scheduled'])
            ->whereBetween('shift_date', [today()->subDay(), today()])
            ->get();

        $scheduledShift = $candidates->filter(function ($shift) use ($now) {
            $startTimeStr = $shift->shift->start_time;
            $endTimeStr = $shift->shift->end_time;
            $shiftStart = Carbon::parse($shift->shift_date->format('Y-m-d') . ' ' . $startTimeStr);
            $shiftEnd = Carbon::parse($shift->shift_date->format('Y-m-d') . ' ' . $endTimeStr);

            if ($shiftEnd->lt($shiftStart)) {
                $shiftEnd->addDay();
            }

            $earliestCheckIn = $shiftStart->copy()->subMinutes(30);
            $shift->calculated_start = $shiftStart;
            
            return $now->between($earliestCheckIn, $shiftEnd);
        })->sortBy(function($shift) use ($now) {
            return $now->diffInMinutes($shift->calculated_start);
        })->first();

        if (!$scheduledShift) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy ca làm việc phù hợp để check-in lúc này.',
            ], 400);
        }

        // Check Late
        $shiftStart = $scheduledShift->calculated_start;
        $lateThreshold = $shiftStart->copy()->addMinutes(15);

        if ($now->gt($lateThreshold)) {
             return response()->json([
                'status' => 'error',
                'message' => 'Bạn đã đi muộn quá 15 phút. Vui lòng gặp quản lý.',
            ], 400);
        }

        // Perform Check-in
        unset($scheduledShift->calculated_start);
        $scheduledShift->checkIn();

        // Invalidate Token (One-time use)
        $employee->invalidateQrToken();

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in thành công! Xin chào ' . $employee->name,
            'type' => 'checkin',
            'employee' => $employee->name
        ]);
    }
}
