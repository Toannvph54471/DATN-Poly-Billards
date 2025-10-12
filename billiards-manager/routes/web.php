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

// Trang chủ
Route::get('/', function () {
    return view('home');
});

// =====================
// 🧩 ADMIN ROUTES
// =====================
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/{id}/update', [UserController::class, 'update'])->name('users.update');

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');

    // Tables
    Route::get('/tables', [TableController::class, 'index'])->name('tables.index');
    Route::delete('tables/{id}', [TableController::class, 'destroy'])->name('tables.destroy');
    Route::get('tables/trashed', [TableController::class, 'trashed'])->name('tables.trashed');
    Route::post('tables/{id}/restore', [TableController::class, 'restore'])->name('tables.restore');
    Route::delete('tables/{id}/force-delete', [TableController::class, 'forceDelete'])->name('tables.forceDelete');

    // 🧑‍💼 Employees
    Route::resource('employees', EmployeeController::class);

    // ⏰ Shifts
    Route::resource('shifts', ShiftController::class);
});

// =====================
// 👨‍🍳 EMPLOYEE ROUTES
// =====================
Route::prefix('employee')->middleware(['auth', 'employee'])->group(function () {
    Route::get('/dashboard', function () {
        return view('employee.dashboard');
    })->name('employee.dashboard');
});
