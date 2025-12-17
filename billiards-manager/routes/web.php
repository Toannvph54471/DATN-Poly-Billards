<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\PromotionClientController;
use App\Http\Controllers\Admin\AdminStatisticsController;
use App\Http\Controllers\AttendanceController;

// Public pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');

// Promotions (client)
Route::get('/promotions', [PromotionClientController::class, 'index'])->name('promotions.index');
Route::get('/promotions/{promotion}', [PromotionClientController::class, 'show'])->name('promotions.show');

// Time clock
Route::get('/time-clock', function () {
    return view('attendance.index');
})->name('time-clock');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/employee.php';
require __DIR__.'/customer.php';

// Attendance (auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/my-qr', [AttendanceController::class, 'myQr'])->name('attendance.my-qr');
    Route::get('/attendance/my-token', [AttendanceController::class, 'getMyQrToken'])->name('attendance.get-token');
});

// Public Scanner
Route::get('/attendance/scan', [AttendanceController::class, 'publicScan'])->name('attendance.scan');

// Simulator
Route::get('/attendance/simulator', [AttendanceController::class, 'simulator'])->name('attendance.simulator');
Route::get('/attendance/test-token/{employeeId}', [AttendanceController::class, 'getTestToken'])->name('attendance.test-token');

// Test Route to Seed Attendance (For Payroll Testing)
Route::get('/test/seed-attendance/{employeeId}', function ($employeeId) {
    $employee = \App\Models\Employee::findOrFail($employeeId);
    
    // Create an attendance record for today: 8:00 AM - 5:00 PM (9 hours - 1 hour lunch? Let's just do 8 hours straight for simplicity)
    // 8:00 to 16:00 = 8 hours
    
    \App\Models\Attendance::create([
        'employee_id' => $employee->id,
        'check_in' => now()->setHour(8)->setMinute(0),
        'check_out' => now()->setHour(16)->setMinute(0),
        'total_minutes' => 480, // 8 hours * 60
        'status' => 'present',
        'approval_status' => 'approved', // Auto approve so it counts
        'date' => now()->toDateString(),
    ]);

    return "Đã tạo dữ liệu chấm công 8 tiếng (8:00 - 16:00) cho nhân viên: " . $employee->name . ". <br> <a href='/admin/payroll'>Quay lại bảng lương</a>";
});

Route::get('/test/verify-admin-checkout', function () {
    // 1. Setup Data
    $user = \App\Models\User::first(); // Assume admin exists
    \Illuminate\Support\Facades\Auth::login($user);
    
    $employee = \App\Models\Employee::first();
    if (!$employee) return "No employee found";

    // Clear existing for clean test
    \App\Models\EmployeeShift::where('employee_id', $employee->id)->delete();
    \App\Models\Attendance::where('employee_id', $employee->id)->delete();

    // Create Active Shift
    $shift = \App\Models\Shift::first(); // Assume a shift exists
    $employeeShift = \App\Models\EmployeeShift::create([
        'employee_id' => $employee->id,
        'shift_id' => $shift->id ?? 1,
        'shift_date' => today(),
        'actual_start_time' => now()->subHours(2), // Started 2 hours ago
        'status' => 'active'
    ]);

    // Create Active Attendance
    $attendance = \App\Models\Attendance::create([
        'employee_id' => $employee->id,
        'check_in' => now()->subHours(2),
        'status' => 'present',
        'approval_status' => 'none'
    ]);

    // 2. Simulate Admin Checkout request
    $controller = new \App\Http\Controllers\AttendanceController();
    $request = new \Illuminate\Http\Request();
    $request->merge(['reason' => 'Testing Admin Checkout']);
    
    try {
        $response = $controller->adminCheckout($request, $attendance->id);
    } catch (\Exception $e) {
        return "Error calling controller: " . $e->getMessage();
    }

    // 3. Verify
    $attendance->refresh();
    $employeeShift->refresh();

    $results = [];
    
    // Check Attendance
    $results[] = $attendance->check_out ? "<span style='color:green'>PASS</span>: Attendance has check_out time" : "<span style='color:red'>FAIL</span>: Attendance check_out is null";
    $results[] = $attendance->admin_checkout_reason === 'Testing Admin Checkout' ? "<span style='color:green'>PASS</span>: Reason saved" : "<span style='color:red'>FAIL</span>: Reason check";
    $results[] = $attendance->total_minutes > 0 ? "<span style='color:green'>PASS</span>: Total minutes > 0 (" . $attendance->total_minutes . ")" : "<span style='color:red'>FAIL</span>: Total minutes invalid";

    // Check Shift
    $results[] = $employeeShift->status === 'completed' ? "<span style='color:green'>PASS</span>: Shift status is 'completed'" : "<span style='color:red'>FAIL</span>: Shift status is " . $employeeShift->status;
    $results[] = $employeeShift->actual_end_time ? "<span style='color:green'>PASS</span>: Shift has end time" : "<span style='color:red'>FAIL</span>: Shift end time is null";

    return implode("<br>", $results);
});


      Route::get('/admin/statistics', [AdminStatisticsController::class, 'index'])
    ->name('admin.statistics');
  

