<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\EmployeeShiftController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ComboController;
use App\Http\Controllers\ComboItemController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BillDetailController;
use App\Http\Controllers\BillTimeUsageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Authentication Routes (Laravel 12 sử dụng Laravel Breeze/Fortify)
Route::get('/', function () {
    return view('welcome');
});

// Custom auth routes nếu cần
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Middleware group for authenticated users
Route::middleware(['auth'])->group(function () {
    
    // Dashboard - accessible for all authenticated users
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile routes - accessible for all authenticated users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // ==================== ADMIN ONLY ROUTES ====================
    Route::middleware(['can:admin'])->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::resource('users', UserController::class);
    });

    // ==================== ADMIN & MANAGER ROUTES ====================
    Route::middleware(['can:manager'])->group(function () {
        Route::resources([
            'employees' => EmployeeController::class,
            'shifts' => ShiftController::class,
            'employee-shifts' => EmployeeShiftController::class,
            'attendances' => AttendanceController::class,
            'payrolls' => PayrollController::class,
            'products' => ProductController::class,
            'combos' => ComboController::class,
            'combo-items' => ComboItemController::class,
            'daily-reports' => DailyReportController::class,
        ]);

        // Report routes
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/daily-reports/generate', [DailyReportController::class, 'generateReport'])
            ->name('daily-reports.generate');
    });

    // ==================== ADMIN, MANAGER & STAFF ROUTES ====================
    Route::middleware(['can:staff'])->group(function () {
        Route::resources([
            'tables' => TableController::class,
            'customers' => CustomerController::class,
            'bills' => BillController::class,
            'bill-details' => BillDetailController::class,
            'bill-time-usage' => BillTimeUsageController::class,
            'payments' => PaymentController::class,
            'reservations' => ReservationController::class,
        ]);

        // Additional staff routes
        Route::get('/bills/{bill}/print', [BillController::class, 'print'])->name('bills.print');
        Route::post('/bills/{bill}/close', [BillController::class, 'close'])->name('bills.close');
        Route::get('/tables/{table}/status', [TableController::class, 'status'])->name('tables.status');
    });

    // ==================== CUSTOMER FACING ROUTES (Staff) ====================
    Route::middleware(['can:staff'])->group(function () {
        Route::get('/pos', [BillController::class, 'pos'])->name('bills.pos');
        Route::post('/bills/quick-create', [BillController::class, 'quickCreate'])->name('bills.quick-create');
        Route::get('/reservations/today', [ReservationController::class, 'today'])->name('reservations.today');
    });
});