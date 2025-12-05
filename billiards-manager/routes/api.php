<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ComboController;
use App\Http\Controllers\AttendanceController;

// API routes (prefix /api is automatic)
Route::post('/tables/available', [ReservationController::class, 'checkAvailability'])->name('api.tables.available');
Route::post('/reservations/search', [ReservationController::class, 'search'])->name('api.reservations.search');
Route::post('/reservations/{reservation}/checkin', [ReservationController::class, 'checkin'])->name('api.reservations.checkin');
Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('api.reservations.cancel');

// API for bills: edit details
Route::post('/bills/{bill}/details', [BillController::class, 'addProduct'])->name('api.bills.add-product');
Route::put('/bills/{bill}/details/{detail}', [BillController::class, 'updateBillDetail'])->name('api.bills.update-detail');
Route::delete('/bills/{bill}/details/{detail}', [BillController::class, 'deleteBillDetail'])->name('api.bills.delete-detail');

// Combos helper API (if used by frontend)
Route::get('/combos/rates-by-category', [ComboController::class, 'getTableRatesByCategory'])->name('api.combos.rates-by-category');
Route::post('/combos/preview-price', [ComboController::class, 'previewComboPrice'])->name('api.combos.preview-price');
Route::get('/combos/calculate-table-price', [ComboController::class, 'calculateTablePriceAPI'])->name('api.combos.calculate-table-price');

// Employee Salary API
Route::post('/employees/{id}/salary', [App\Http\Controllers\EmployeeController::class, 'updateSalary'])->name('api.employees.update-salary');
Route::post('/payroll/generate', [App\Http\Controllers\PayrollController::class, 'generate']);

Route::post('/attendance/scan', [AttendanceController::class, 'processScan']);
Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
Route::post('/attendance/submit-late-reason', [AttendanceController::class, 'submitLateReason']);
Route::post('/attendance/check-out', [App\Http\Controllers\AttendanceController::class, 'checkOut'])->name('api.attendance.check-out');
Route::get('/attendance/active', [App\Http\Controllers\AttendanceController::class, 'getActiveEmployees']);
Route::get('/attendance/server-time', [App\Http\Controllers\AttendanceController::class, 'getServerTime']);
