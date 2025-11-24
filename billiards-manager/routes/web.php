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

// Employee Attendance Routes (Public/Shared area)
Route::middleware(['auth', 'role:admin,manager,employee'])->group(function () {
    Route::get('/attendance/scan', [App\Http\Controllers\AttendanceController::class, 'scanQrCode'])->name('attendance.scan');
});
