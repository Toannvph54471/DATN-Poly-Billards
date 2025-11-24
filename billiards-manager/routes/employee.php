<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\BillDetailController;
use App\Http\Controllers\BillTimeUsageController;
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

        // ============================
        // DASHBOARD
        // ============================
        Route::get('/pos-dashboard', [DashboardController::class, 'posDashboard'])->name('pos.dashboard');

        // ============================
        // TABLE MANAGEMENT & ACTIONS
        // ============================
        Route::prefix('tables')->name('tables.')->group(function () {
            // Check in / Check out
            Route::post('/{id}/checkin', [TableController::class, 'checkin'])->name('checkin');
            Route::post('/{id}/checkout', [TableController::class, 'checkout'])->name('checkout');
            
            // Xem chi tiết bàn
            Route::get('/{id}/detail', [TableController::class, 'showDetail'])->name('detail');

            // Simple dashboard cho POS
            Route::get('/simple-dashboard', [TableController::class, 'simpleDashboard'])->name('simple-dashboard');
        });

        // ============================
        // BILL MANAGEMENT (POS functions)
        // ============================
        Route::prefix('bills')->name('bills.')->group(function () {
            // Tạo bill

            Route::get('/index', [BillController::class, 'index'])->name('index');
            Route::get('/{id}/show', [BillController::class, 'show'])->name('show');
            Route::post('/create', [BillController::class, 'createBill'])->name('create');
            Route::post('/quick-create', [BillController::class, 'createQuickBill'])->name('quick-create');

            // Thêm sản phẩm/combo vào bill
            Route::post('/{id}/add-product', [BillController::class, 'addProductToBill'])->name('add-product');
            Route::post('/{id}/add-combo', [BillController::class, 'addComboToBill'])->name('add-combo');

            // Xóa sản phẩm khỏi bill
            Route::delete('/{bill}/products/{billDetail}', [BillController::class, 'removeProductFromBill'])->name('remove-product');

            // Quản lý thời gian chơi
            Route::post('/{id}/start-playing', [BillController::class, 'startPlaying'])->name('start-playing');
            Route::post('/{id}/pause', [BillController::class, 'pauseTime'])->name('pause');
            Route::post('/{id}/resume', [BillController::class, 'resumeTime'])->name('resume');

            // Cập nhật tổng tiền
            Route::post('/{id}/update-total', [BillController::class, 'updateBillTotal'])->name('update-total');

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
            // Hiển thị trang thanh toán
            Route::get('/{id}/payment', [PaymentController::class, 'showPayment'])->name('payment-page');
            
            // Xử lý thanh toán
            Route::post('/{id}/process', [PaymentController::class, 'processPayment'])->name('process-payment');
        });

        // ============================
        // RESERVATION MANAGEMENT
        // ============================
        Route::prefix('reservations')->name('reservations.')->group(function () {
            // CRUD cơ bản
            Route::get('/', [ReservationController::class, 'index'])->name('index');
            Route::get('/create', [ReservationController::class, 'create'])->name('create');
            Route::post('/', [ReservationController::class, 'store'])->name('store');
            Route::get('/{id}', [ReservationController::class, 'show'])->name('show');

            // Employee có thể checkin reservation
            Route::post('/{id}/checkin', [ReservationController::class, 'checkin'])->name('checkin');
        });
    });