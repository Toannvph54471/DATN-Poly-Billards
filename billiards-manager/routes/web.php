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
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('home');
});

// Route cho Admin và Manager
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    // Users
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}/update', [UserController::class, 'update'])->name('admin.users.update');

    // Employees
    Route::get('/employees', [EmployeeController::class, 'index'])->name('admin.employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('admin.employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('admin.employees.store');
    Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('admin.employees.show');
    Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('admin.employees.edit');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('admin.employees.update');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('admin.employees.destroy');

    // Combos
    Route::get('/combos', [ComboController::class, 'index'])->name('admin.combos.index');
    Route::get('/combos/create', [ComboController::class, 'create'])->name('admin.combos.create');
    Route::post('/combos', [ComboController::class, 'store'])->name('admin.combos.store');
    Route::get('/combos/{id}', [ComboController::class, 'show'])->name('admin.combos.show');
    Route::get('/combos/{id}/edit', [ComboController::class, 'edit'])->name('admin.combos.edit');
    Route::put('/combos/{id}', [ComboController::class, 'update'])->name('admin.combos.update');
    Route::delete('/combos/{id}', [ComboController::class, 'destroy'])->name('admin.combos.destroy');

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');

    // Tables
    Route::get('/tables', [TableController::class, 'index'])->name('admin.tables.index');
    Route::delete('tables/{id}', [TableController::class, 'destroy'])->name('admin.tables.destroy');
    Route::get('tables/trashed', [TableController::class, 'trashed'])->name('admin.tables.trashed');
    Route::post('tables/{id}/restore', [TableController::class, 'restore'])->name('admin.tables.restore');
    Route::delete('tables/{id}/force-delete', [TableController::class, 'forceDelete'])->name('admin.tables.forceDelete');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');

    // Shift
    Route::get('/shifts', [ShiftController::class, 'index'])->name('admin.shifts.index');
    Route::get('/shifts/create', [ShiftController::class, 'create'])->name('admin.shifts.create');
    Route::post('/shifts/store', [ShiftController::class, 'store'])->name('admin.shifts.store');
    Route::get('/shifts/{id}/edit', [ShiftController::class, 'edit'])->name('admin.shifts.edit');
    Route::put('/shifts/{id}/update', [ShiftController::class, 'update'])->name('admin.shifts.update');

    Route::get('/shiftE', [ShiftController::class, 'shiftEmployee'])->name('admin.shiftEmployee.index');
    Route::post('/shiftE/schedule', [ShiftController::class, 'scheduleShifts'])->name('admin.shiftEmployee.schedule');
    Route::post('/shiftE/save-weekly', [ShiftController::class, 'saveWeeklySchedule'])->name('admin.shiftEmployee.saveWeekly');
    Route::post('/shiftE/bulk-schedule', [ShiftController::class, 'bulkScheduleShifts'])->name('admin.shiftEmployee.bulkSchedule');
});
