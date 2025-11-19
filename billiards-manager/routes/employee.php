<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\BillDetailController;
use App\Http\Controllers\BillTimeUsageController;
use App\Http\Controllers\PaymentController;

// Employee area (POS / thao tác bán hàng)
Route::prefix('employee')
    ->name('employee.')
    ->middleware(['auth','role:admin,manager,employee'])
    ->group(function () {

    // Bills: employee thao tác (tương tự admin nhưng scoped)
    Route::prefix('bills')->name('bills.')->group(function () {
        Route::post('/create', [BillController::class, 'createBill'])->name('create');
        Route::post('/{id}/add-combo', [BillController::class, 'addComboToBill'])->name('add-combo');
        Route::post('/{id}/add-product', [BillController::class, 'addProductToBill'])->name('add-product');
        Route::post('/{id}/process-payment', [BillController::class, 'processPayment'])->name('process-payment');
        Route::get('/{id}/payment', [BillController::class, 'showPayment'])->name('payment-page');
        Route::post('/{id}/update-total', [BillController::class, 'updateBillTotal'])->name('update-total');

        Route::post('/{id}/start-playing', [BillController::class, 'startPlaying'])->name('start-playing');
        Route::post('/{id}/pause', [BillController::class, 'pause'])->name('pause');
        Route::post('/{id}/resume', [BillController::class, 'resume'])->name('resume');
    });

    // Table quick actions for staff
    Route::post('tables/{id}/checkin', [TableController::class, 'checkin'])->name('tables.checkin');
    Route::post('tables/{id}/checkout', [TableController::class, 'checkout'])->name('tables.checkout');

    // Reservations (employee can create or list)
    Route::resource('reservations', ReservationController::class)->only(['index','create','store','show'])->names('reservations');
});
