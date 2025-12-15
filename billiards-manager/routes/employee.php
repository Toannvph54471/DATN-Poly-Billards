<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PosDashboardController;

/*
|--------------------------------------------------------------------------
| EMPLOYEE ROUTES (Nhân viên có thể truy cập)
|--------------------------------------------------------------------------
| Employee, Admin, Manager đều có thể truy cập các route này
| Dùng chung giao diện nhưng phân quyền bằng middleware
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin,manager,employee'])
    ->group(function () {

        // Dashboard cho nhân viên
        Route::get('/pos-dashboard', [PosDashboardController::class, 'posDashboard'])->name('pos.dashboard');
        Route::get('/my-profile', [EmployeeController::class, 'myProfile'])->name('my-profile');
        Route::post('/{employee}/change-password', [EmployeeController::class, 'changePassword'])->name('change-password');
        Route::get('/my-schedule', [EmployeeController::class, 'mySchedule'])->name('schedule');
        // ============================
        // TABLE ACTIONS FOR STAFF
        // ============================
        Route::prefix('tables')->name('tables.')->group(function () {

            // Employee có thể xem chi tiết bàn
            Route::get('/{id}/detail', [TableController::class, 'showDetail'])->name('detail');

            // Simple dashboard cho POS
            Route::get('/simple-dashboard', [TableController::class, 'simpleDashboard'])->name('simple-dashboard');
        });

        // ============================
        // BILL ACTIONS (POS functions)
        // ============================
        Route::prefix('bills')->name('bills.')->group(function () {
            Route::get('index', [BillController::class, 'index'])->name('index');
            Route::post('filter', [BillController::class, 'filter'])->name('filter');
            Route::post('reset', [BillController::class, 'resetFilter'])->name('reset');
            Route::get('/{id}/show', [BillController::class, 'show'])->name('show');
            Route::post('/create', [BillController::class, 'createBill'])->name('create');
            Route::post('/quick-create', [BillController::class, 'createQuickBill'])->name('quick-create');
            Route::post('/{id}/add-product', [BillController::class, 'addProductToBill'])->name('add-product');
            Route::post('/{id}/add-combo', [BillController::class, 'addComboToBill'])->name('add-combo');
            Route::post('/{id}/start-playing', [BillController::class, 'startPlaying'])->name('start-playing');
            Route::post('/{id}/stop-combo', [BillController::class, 'stopComboTime'])->name('stop-combo');
            Route::post('/{id}/pause', [BillController::class, 'pauseTime'])->name('pause');
            Route::post('/{id}/resume', [BillController::class, 'resumeTime'])->name('resume');
            Route::delete('/{bill}/products/{billDetail}', [BillController::class, 'removeProductFromBill'])->name('remove-product');

            // Chuyển bàn
            Route::get('/{id}/transfer', [BillController::class, 'showTransferForm'])->name('transfer-form');
            Route::post('/transfer', [BillController::class, 'transferTable'])->name('transfer');
            Route::get('/transfer/available/{billId}', [BillController::class, 'getAvailableTables'])->name('transfer.available');
            Route::get('/{id}/print', [BillController::class, 'printBill'])->name('print');
        });

        // ============================
        // PAYMENT ACTIONS (POS)
        // ============================
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/{id}/payment', [PaymentController::class, 'showPayment'])->name('payment-page');
            Route::post('/{id}/process', [PaymentController::class, 'processPayment'])->name('process-payment');
            Route::post('/payments/check-promotion', [PaymentController::class, 'checkPromotion'])->name('check-promotion');
            Route::post('/payments/apply-promotion', [PaymentController::class, 'applyPromotion'])->name('apply-promotion');
            Route::post('/payments/remove-promotion', [PaymentController::class, 'removePromotion'])->name('remove-promotion');

            // VNPay Routes
            Route::post('/vnpay/create', [PaymentController::class, 'createVNPayPayment'])->name('vnpay.create');
            Route::get('/vnpay/return', [PaymentController::class, 'vnpayReturn'])->name('vnpay.return');
            Route::post('/vnpay/ipn', [PaymentController::class, 'vnpayIPN'])->name('vnpay.ipn');

            Route::get('/{id}/status', [PaymentController::class, 'checkPaymentStatus'])->name('payments.status');
            Route::get('/bills/{id}/status', [PaymentController::class, 'checkBillStatus'])->name('bills.status');
        });

        // ============================
        // RESERVATION FOR EMPLOYEE
        // ============================
        Route::prefix('reservations')->name('reservations.')->group(function () {
            Route::get('/', [ReservationController::class, 'index'])->name('index');
            Route::get('/create', [ReservationController::class, 'create'])->name('create');
            Route::post('/', [ReservationController::class, 'store'])->name('store');
            Route::get('/{id}', [ReservationController::class, 'show'])->name('show');

            // Employee có thể checkin reservation
            Route::post('/{id}/checkin', [ReservationController::class, 'checkin'])->name('checkin');
        });
    });
