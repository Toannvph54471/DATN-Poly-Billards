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

// Admin statistics

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/statistics', [AdminStatisticsController::class, 'index'])
        ->name('admin.statistics.index');

});


      Route::get('/admin/statistics', [AdminStatisticsController::class, 'index'])
    ->name('admin.statistics');
  

