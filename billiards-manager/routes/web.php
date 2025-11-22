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
require __DIR__ . '/admin.php';
require __DIR__ . '/employee.php';
require __DIR__ . '/customer.php';
