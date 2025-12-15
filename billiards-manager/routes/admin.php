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
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\Admin\AdminStatisticsController;

/*
|--------------------------------------------------------------------------
| ADMIN & MANAGER ROUTES
|--------------------------------------------------------------------------
| Chỉ Admin + Manager được vào các route này
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin,manager'])
    ->group(function () {

        // Dashboard

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('chart-data', [DashboardController::class, 'getChartData'])->name('chart-data');


        Route::get('/statistics', [AdminStatisticsController::class, 'index'])->name('statistics');
        Route::get('/quick-stats', [DashboardController::class, 'getQuickStats'])->name('quick-stats');
        Route::get('/top-products', [DashboardController::class, 'getTopProductsData'])->name('top-products');
        Route::get('/table-stats', [DashboardController::class, 'getTableStatsData'])->name('table-stats');
        Route::get('/report-data', [DashboardController::class, 'getReportData'])->name('report-data');



        Route::get('/admin/dashboard/debug', [DashboardController::class, 'debugToday'])->name('dashboard.debug');
        /*
        |--------------------------------------------------------------------------
        | ADMIN ONLY
        |--------------------------------------------------------------------------
        */
        Route::middleware(['role:admin'])->group(function () {
            // Users
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::post('/users/{id}/update', [UserController::class, 'update'])->name('users.update');

            // Roles
            Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');

            // Employees
            Route::resource('employees', EmployeeController::class)->names('employees');
            Route::get('my-profile', [EmployeeController::class, 'myProfile'])->name('my-profile');

            // Payroll Management
            Route::get('/payroll', [PayrollController::class, 'adminIndex'])->name('payroll.index');
            Route::post('/payroll/lock', [PayrollController::class, 'lockMonth'])->name('payroll.lock');
            Route::post('/payroll/generate-all', [PayrollController::class, 'generateAll'])->name('payroll.generate-all');
            Route::post('/payroll/pay-all', [PayrollController::class, 'payAll'])->name('payroll.pay-all');
            Route::post('/payroll/{id}/pay', [PayrollController::class, 'markAsPaid'])->name('payroll.pay');

            // Attendance Monitoring
            Route::get('/attendance/monitor', [AttendanceController::class, 'monitor'])->name('attendance.monitor');
            Route::post('/attendance/{id}/approve-late', [AttendanceController::class, 'approveLate'])->name('attendance.approve-late');
            Route::post('/attendance/{id}/reject-late', [AttendanceController::class, 'rejectLate'])->name('attendance.reject-late');
            Route::post('/attendance/{id}/admin-checkout', [AttendanceController::class, 'adminCheckout'])->name('attendance.admin-checkout');
            Route::get('/attendance/manual-history', [AttendanceController::class, 'manualCheckoutHistory'])->name('attendance.manual-history');
        });

        /*
        |--------------------------------------------------------------------------
        | ADMIN + MANAGER (Management functions)
        |--------------------------------------------------------------------------
        */

        // Combos Management
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

        // Tables Management
        Route::resource('tables', TableController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
            ->names('tables');

        Route::get('tables/{id}/detail', [TableController::class, 'showDetail'])->name('tables.detail');
        Route::get('tables/simple-dashboard', [TableController::class, 'simpleDashboard'])->name('tables.simple-dashboard');
        Route::get('{billId}/check-combo-time', [BillController::class, 'checkComboTimeStatus'])->name('tables.check-combo-time');
        // TẠM DỪNG BÀN - THÊM VÀO ĐÂY
        Route::post('/tables/{table}/pause', [TableController::class, 'pause'])->name('tables.pause');
        Route::post('/tables/{table}/resume', [TableController::class, 'resume'])->name('tables.resume');
        // routes/admin.php
        Route::post('/update-positions', [TableController::class, 'updatePositions'])->name('update-positions');

        Route::get('simple-dashboard', [TableController::class, 'simpleDashboard'])->name('tables.simple-dashboard');
        Route::post('save-layout', [TableController::class, 'saveLayout'])->name('tables.save-layout');
        Route::post('reset-layout', [TableController::class, 'resetLayout'])->name('tables.reset-layout');

        Route::get('tables/trashed', [TableController::class, 'trashed'])->name('tables.trashed');
        Route::post('tables/{id}/restore', [TableController::class, 'restore'])->name('tables.restore');
        Route::delete('tables/{id}/force-delete', [TableController::class, 'forceDelete'])->name('tables.forceDelete');

        // Table Rates Management
        Route::resource('table_rates', TableRateController::class)->except(['show'])->names('table_rates');
        Route::get('table_rates/trashed', [TableRateController::class, 'trashed'])->name('table_rates.trashed');
        Route::post('table_rates/{id}/restore', [TableRateController::class, 'restore'])->name('table_rates.restore');
        Route::delete('table_rates/{id}/force-delete', [TableRateController::class, 'forceDelete'])->name('table_rates.forceDelete');

        // Bills Management (Advanced functions)
        Route::prefix('bills')->name('bills.')->group(function () {
            // Advanced bill functions only for admin/manager
            Route::get('index', [BillController::class, 'index'])->name('index');
            Route::post('filter', [BillController::class, 'filter'])->name('filter');
            Route::post('reset', [BillController::class, 'resetFilter'])->name('reset');
            Route::post('/bills/check-new', [BillController::class, 'checkNewBills'])->name('check-new');
            Route::get('/{id}/show', [BillController::class, 'show'])->name('show');
            Route::post('/{id}/switch-regular', [BillController::class, 'switchToRegularTime'])->name('switch-regular');
            Route::post('/{id}/extend-combo', [BillController::class, 'extendComboTime'])->name('extend-combo');
            Route::post('/{id}/update-total', [BillController::class, 'updateBillTotal'])->name('update-total');
            Route::get('/{id}/time-info', [BillController::class, 'getBillTimeInfo'])->name('time-info');
            Route::post('/{id}/convert-to-quick', [BillController::class, 'convertToQuick'])->name('convert-to-quick');
            Route::post('/{id}/stop-combo', [BillController::class, 'stopComboTime'])->name('stop-combo');
            Route::get('/{id}/transfer', [BillController::class, 'showTransferForm'])->name('transfer-form');
            // Thêm vào routes/web.php
            Route::post('/bills/{bill}/payment-success', [BillController::class, 'processPaymentSuccess'])->name('payment-success');

            // Print bill
            Route::get('/{id}/print', [BillController::class, 'printBill'])->name('print');
            Route::get('/print-multiple', [BillController::class, 'printBillMultiple'])->name('print-multiple');
        });

        // Payments Management
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/{id}/payment', [PaymentController::class, 'showPayment'])->name('payment-page');
            Route::get('/payment', [PaymentController::class, 'showPaymentMultiple'])->name('payment-page-multiple');
            Route::post('/{id}/process', [PaymentController::class, 'processPayment'])->name('process-payment');
            Route::post('/pprocess/multiple', [PaymentController::class, 'processPaymentMultiple'])->name('process-payment-multiple');
            Route::post('/payments/check-promotion', [PaymentController::class, 'checkPromotion'])->name('check-promotion');
            Route::post('/payments/apply-promotion', [PaymentController::class, 'applyPromotion'])->name('apply-promotion');
            Route::post('/payments/remove-promotion', [PaymentController::class, 'removePromotion'])->name('remove-promotion');
        });

        // Products Management
        Route::resource('products', ProductController::class)->names('products');
        Route::get('products/trashed', [ProductController::class, 'trashed'])->name('products.trashed');
        Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.forceDelete');

        // Shifts Management
        Route::resource('shifts', ShiftController::class)->only(['index', 'create', 'store', 'edit', 'update'])->names('shifts');
        Route::get('shift-employee', [ShiftController::class, 'shiftEmployee'])->name('shiftEmployee.index');
        Route::post('shiftE/schedule', [ShiftController::class, 'scheduleShifts'])->name('shiftEmployee.schedule');
        Route::post('shiftE/save-weekly', [ShiftController::class, 'saveWeeklySchedule'])->name('shiftEmployee.saveWeekly');
        Route::post('shiftE/bulk-schedule', [ShiftController::class, 'bulkScheduleShifts'])->name('shiftEmployee.bulkSchedule');
        Route::post('/shifts/copy-previous-week', [ShiftController::class, 'copyPreviousWeek'])->name('shifts.copy-previous-week');

        // Promotions Management
        Route::resource('promotions', PromotionController::class)->except(['destroy'])->names('promotions');
        Route::get('promotions/{id}', [PromotionController::class, 'show'])->name('promotions.show');
        Route::get('promotions/trashed', [PromotionController::class, 'trashed'])->name('promotions.trashed');
        Route::post('promotions/{id}/restore', [PromotionController::class, 'restore'])->name('promotions.restore');
        Route::delete('promotions/{id}/destroy', [PromotionController::class, 'destroy'])->name('promotions.destroy');
        Route::delete('promotions/{id}/force-delete', [PromotionController::class, 'forceDelete'])->name('promotions.forceDelete');

        // Customers Management
        Route::resource('customers', CustomerController::class)->names('customers');
        Route::get('customers/trashed', [CustomerController::class, 'trash'])->name('customers.trashed');
        Route::post('customers/{id}/restore', [CustomerController::class, 'restore'])->name('customers.restore');
        Route::delete('customers/{id}/force-delete', [CustomerController::class, 'forceDelete'])->name('customers.force-delete');
    });
