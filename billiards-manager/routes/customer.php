<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ClientProfileController;
use App\Http\Controllers\BillController as ClientBillController;
use App\Http\Controllers\BillHistoryController;

// Routes for authenticated customers (client area)
Route::middleware(['auth','role:customer'])->group(function () {

    // Reservation (customer)
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservation', [ReservationController::class, 'create'])->name('reservation.create');
    Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
    Route::get('/reservations/track', [ReservationController::class, 'track'])->name('reservations.track');
    Route::post('/reservations/search', [ReservationController::class, 'search'])->name('reservations.search');

    // Profile
    Route::prefix('profile')->name('client.profile.')->group(function () {
        Route::get('/', [ClientProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ClientProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ClientProfileController::class, 'update'])->name('update');
    });

    // Bill history (customer)
    Route::get('/bills', [ClientBillController::class, 'index'])->name('bills.index');
    Route::get('/bills/{id}', [ClientBillController::class, 'show'])->name('bills.show');
});
