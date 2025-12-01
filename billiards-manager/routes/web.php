<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\PromotionClientController;

// Public pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');

// Promotions (client)
Route::get('/promotions', [PromotionClientController::class, 'index'])->name('promotions.index');
Route::get('/promotions/{promotion}', [PromotionClientController::class, 'show'])->name('promotions.show');

// Tải các nhóm route tách file
Route::get('/time-clock', function () {
    return view('attendance.index');
})->name('time-clock');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__ . '/customer.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/my-qr', [App\Http\Controllers\AttendanceController::class, 'myQr'])->name('attendance.my-qr');
    Route::get('/attendance/my-token', [App\Http\Controllers\AttendanceController::class, 'getMyQrToken'])->name('attendance.get-token');
});

// Public Scanner Route (Kiosk Mode)
Route::get('/attendance/scan', [App\Http\Controllers\AttendanceController::class, 'publicScan'])->name('attendance.scan');

// Simulator Routes (Localhost Testing)
Route::get('/attendance/simulator', [App\Http\Controllers\AttendanceController::class, 'simulator'])->name('attendance.simulator');
Route::get('/attendance/test-token/{employeeId}', [App\Http\Controllers\AttendanceController::class, 'getTestToken'])->name('attendance.test-token');
