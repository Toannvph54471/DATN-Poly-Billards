<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ComboController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TableRateController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ComboItemController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;

// Admin + Manager area
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth','role:admin,manager'])
    ->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::post('/users/{id}/update', [UserController::class, 'update'])->name('users.update');

        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    });

    // Employees (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('employees', EmployeeController::class)->names('employees');
    });

    // Combos (Admin/Manager)
    Route::prefix('combos')->name('combos.')->group(function () {
        Route::get('/', [ComboController::class, 'index'])->name('index');
        Route::get('/create', [ComboController::class, 'create'])->name('create');
        Route::post('/', [ComboController::class, 'store'])->name('store');
        Route::get('/{id}', [ComboController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ComboController::class, 'edit'])->name('edit');
        Route::post('/{id}', [ComboController::class, 'update'])->name('update');
        Route::delete('/{id}', [ComboController::class, 'destroy'])->name('destroy');
        Route::get('/trash', [ComboController::class, 'trash'])->name('trash');
        Route::post('/restore/{id}', [ComboController::class, 'restore'])->name('restore');
        Route::delete('/force-delete/{id}', [ComboController::class, 'forceDelete'])->name('forceDelete');
    });

    // Tables & Table rates (Admin/Manager)
    Route::resource('tables', TableController::class)->only(['index','create','store','edit','update','destroy'])->names('tables');
    Route::get('tables/trashed', [TableController::class, 'trashed'])->name('tables.trashed');
    Route::post('tables/{id}/restore', [TableController::class, 'restore'])->name('tables.restore');
    Route::delete('tables/{id}/force-delete', [TableController::class, 'forceDelete'])->name('tables.forceDelete');
    Route::get('tables/{id}/detail', [TableController::class, 'showDetail'])->name('tables.detail');

    Route::resource('table_rates', TableRateController::class)->except(['show'])->names('table_rates');
    Route::get('table_rates/trashed', [TableRateController::class, 'trashed'])->name('table_rates.trashed');
    Route::post('table_rates/{id}/restore', [TableRateController::class, 'restore'])->name('table_rates.restore');
    Route::delete('table_rates/{id}/force-delete', [TableRateController::class, 'forceDelete'])->name('table_rates.forceDelete');

    // Bills (Admin/Manager)
    Route::prefix('bills')->name('bills.')->group(function () {
        Route::post('/create', [BillController::class, 'createBill'])->name('create');
        Route::post('/{id}/add-combo', [BillController::class, 'addComboToBill'])->name('add-combo');
        Route::post('/{id}/add-product', [BillController::class, 'addProductToBill'])->name('add-product');
        Route::post('/{id}/switch-regular', [BillController::class, 'switchToRegularTime'])->name('switch-regular');
        Route::post('/{id}/extend-combo', [BillController::class, 'extendComboTime'])->name('extend-combo');
        Route::post('/{id}/process-payment', [BillController::class, 'processPayment'])->name('process-payment');
        Route::get('/{id}/payment', [BillController::class, 'showPayment'])->name('payment-page');
        Route::post('/{id}/update-total', [BillController::class, 'updateBillTotal'])->name('update-total');
        Route::get('/{id}/time-info', [BillController::class, 'getBillTimeInfo'])->name('time-info');

        Route::post('/quick-create', [BillController::class, 'createQuickBill'])->name('quick-create');
        Route::post('/{id}/convert-to-quick', [BillController::class, 'convertToQuick'])->name('convert-to-quick');
        Route::post('/{id}/start-playing', [BillController::class, 'startPlaying'])->name('start-playing');

        Route::post('/{id}/pause', [BillController::class, 'pauseTime'])->name('pause');
        Route::post('/{id}/resume', [BillController::class, 'resumeTime'])->name('resume');
    });

    // Products (Admin/Manager)
    Route::resource('products', ProductController::class)->names('products');
    Route::get('products/trashed', [ProductController::class, 'trashed'])->name('products.trashed');
    Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::delete('products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.forceDelete');

    // Shifts (Admin/Manager)
    Route::resource('shifts', ShiftController::class)->only(['index','create','store','edit','update'])->names('shifts');
    Route::get('shift-employee', [ShiftController::class, 'shiftEmployee'])->name('shiftEmployee.index');
    Route::post('shiftE/schedule', [ShiftController::class, 'scheduleShifts'])->name('shiftEmployee.schedule');
    Route::post('shiftE/save-weekly', [ShiftController::class, 'saveWeeklySchedule'])->name('shiftEmployee.saveWeekly');
    Route::post('shiftE/bulk-schedule', [ShiftController::class, 'bulkScheduleShifts'])->name('shiftEmployee.bulkSchedule');

    // Promotions (Admin/Manager)
    Route::resource('promotions', PromotionController::class)->except(['destroy'])->names('promotions');
    Route::get('promotions/{id}', [PromotionController::class, 'show'])->name('promotions.show');
    Route::get('promotions/trashed', [PromotionController::class, 'trashed'])->name('promotions.trashed');
    Route::post('promotions/{id}/restore', [PromotionController::class, 'restore'])->name('promotions.restore');
    Route::delete('promotions/{id}/destroy', [PromotionController::class, 'destroy'])->name('promotions.destroy');
    Route::delete('promotions/{id}/force-delete', [PromotionController::class, 'forceDelete'])->name('promotions.forceDelete');

    // Customers (Admin/Manager)
    Route::resource('customers', CustomerController::class)->names('customers');
    Route::get('customers/trashed', [CustomerController::class, 'trash'])->name('customers.trashed');
    Route::post('customers/{id}/restore', [CustomerController::class, 'restore'])->name('customers.restore');
    Route::delete('customers/{id}/force-delete', [CustomerController::class, 'forceDelete'])->name('customers.force-delete');

    // Combos - helper API endpoints (kept here if used by admin UI)
    Route::get('combos-api/rates-by-category', [ComboController::class, 'getTableRatesByCategory'])->name('combos.rates-by-category');
    Route::post('combos-api/preview-price', [ComboController::class, 'previewComboPrice'])->name('combos.preview-price');
    Route::get('combos-api/calculate-table-price', [ComboController::class, 'calculateTablePriceAPI'])->name('combos.calculate-table-price');

    // Payment routes (Admin)
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('reservations/{reservation}/payment', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
    Route::post('payments/{payment}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');

    // Reports
    Route::get('reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
});
