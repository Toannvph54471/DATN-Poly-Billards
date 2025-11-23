<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;

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
        Route::get('/pos-dashboard', [DashboardController::class, 'posDashboard'])->name('pos.dashboard');

        // ============================
        // TABLE ACTIONS FOR STAFF
        // ============================
        Route::prefix('tables')->name('tables.')->group(function () {
            Route::post('/{id}/checkin', [TableController::class, 'checkin'])->name('checkin');
            Route::post('/{id}/checkout', [TableController::class, 'checkout'])->name('checkout');

            // Employee có thể xem chi tiết bàn
            Route::get('/{id}/detail', [TableController::class, 'showDetail'])->name('detail');

            // Simple dashboard cho POS
            Route::get('/simple-dashboard', [TableController::class, 'simpleDashboard'])->name('simple-dashboard');
        });

        // ============================
        // BILL ACTIONS (POS functions)
        // ============================
        Route::prefix('bills')->name('bills.')->group(function () {
            // Bill
            Route::get('index', [BillController::class, 'index'])->name('index');
            Route::get('/{id}/show', [BillController::class, 'show'])->name('show');
            // Tạo bill
            Route::post('/create', [BillController::class, 'createBill'])->name('create');
            Route::post('/quick-create', [BillController::class, 'createQuickBill'])->name('quick-create');

            // Thêm sản phẩm/combo
            Route::post('/{id}/add-product', [BillController::class, 'addProductToBill'])->name('add-product');
            Route::post('/{id}/add-combo', [BillController::class, 'addComboToBill'])->name('add-combo');

            // Quản lý thời gian
            Route::post('/{id}/start-playing', [BillController::class, 'startPlaying'])->name('start-playing');
            Route::post('/{id}/pause', [BillController::class, 'pauseTime'])->name('pause');
            Route::post('/{id}/resume', [BillController::class, 'resumeTime'])->name('resume');

            // Xóa sản phẩm
            Route::delete('/{bill}/products/{billDetail}', [BillController::class, 'removeProductFromBill'])->name('remove-product');

            // Chuyển bàn
            Route::get('/{id}/transfer', [BillController::class, 'showTransferForm'])->name('transfer-form');
            Route::post('/transfer', [BillController::class, 'transferTable'])->name('transfer');
            Route::get('/transfer/available/{billId}', [BillController::class, 'getAvailableTables'])->name('transfer.available');

            // In bill
            Route::get('/{id}/print', [BillController::class, 'printBill'])->name('print');
        });

        // ============================
        // PAYMENT ACTIONS (POS)
        // ============================
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/{id}/payment', [PaymentController::class, 'showPayment'])->name('payment-page');
            Route::post('/{id}/process', [PaymentController::class, 'processPayment'])->name('process-payment');
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
