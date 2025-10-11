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
    return view('home');
});

// Route cho Admin và Manager
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Users
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::post('/users/{id}/update', [UserController::class, 'update'])->name('admin.users.update');
    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    // Tables
    Route::get('/tables', [TableController::class, 'index'])->name('admin.tables.index');
    Route::delete('tables/{id}', [TableController::class, 'destroy'])->name('admin.tables.destroy');
    Route::get('tables/trashed', [TableController::class, 'trashed'])->name('admin.tables.trashed');
    Route::post('tables/{id}/restore', [TableController::class, 'restore'])->name('admin.tables.restore');
    Route::delete('tables/{id}/force-delete', [TableController::class, 'forceDelete'])->name('admin.tables.forceDelete');
});

// Route cho Employee
Route::prefix('employee')->middleware(['auth', 'employee'])->group(function () {
    Route::get('/dashboard', function () {
        return view('employee.dashboard');
    })->name('employee.dashboard');
});
