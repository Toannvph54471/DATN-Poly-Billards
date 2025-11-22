<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationController;

// NHÂN VIÊN CHỈ ĐƯỢC DÙNG MỘT SỐ CHỨC NĂNG TRONG ADMIN
// Không đổi URL, chỉ kiểm soát quyền
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin,manager,employee'])
    ->group(function () {

        // ============================
        // TABLE ACTIONS FOR STAFF
        // ============================
        Route::post('tables/{id}/checkin', [TableController::class, 'checkin'])
            ->name('tables.checkin');

        Route::post('tables/{id}/checkout', [TableController::class, 'checkout'])
            ->name('tables.checkout');

        // ============================
        // BILL ACTIONS (EMPLOYEE dùng POS)
        // ============================
        Route::prefix('bills')->name('bills.')->group(function () {

            Route::post('/create', [BillController::class, 'createBill'])->name('create');
            Route::post('/{id}/add-product', [BillController::class, 'addProductToBill'])->name('add-product');
            Route::post('/{id}/add-combo', [BillController::class, 'addComboToBill'])->name('add-combo');
            Route::post('/{id}/start-playing', [BillController::class, 'startPlaying'])->name('start-playing');

            Route::post('/{id}/pause', [BillController::class, 'pauseTime'])->name('pause');
            Route::post('/{id}/resume', [BillController::class, 'resumeTime'])->name('resume');

            Route::post('/{id}/process-payment', [BillController::class, 'processPayment'])->name('process-payment');
            Route::get('/{id}/payment', [BillController::class, 'showPayment'])->name('payment-page');

            // chuyển bàn
            Route::get('/transfer/{billId}', [BillController::class, 'showTransferForm'])->name('transfer-form');
            Route::post('/transfer', [BillController::class, 'transferTable'])->name('transfer');
            Route::get('/transfer/available/{billId}', [BillController::class, 'getAvailableTables'])->name('transfer.available');
        });

        // ============================
        // RESERVATION FOR EMPLOYEE
        // ============================
        Route::resource('reservations', ReservationController::class)
            ->only(['index', 'create', 'store', 'show'])
            ->names('reservations');

    });

