<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\EmployeeShiftController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ComboController;
use App\Http\Controllers\ComboItemController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BillDetailController;
use App\Http\Controllers\BillTimeUsageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\ComboTimeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\PromotionClientController;



Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');

// Promotions
Route::get('/promotions', [PromotionClientController::class, 'index'])->name('promotions.index');
Route::get('/promotions/{promotion}', [PromotionClientController::class, 'show'])->name('promotions.show');


Route::prefix('api')->group(function () {
    Route::post('/tables/available', [ReservationController::class, 'checkAvailability'])->name('api.tables.available');
    Route::post('/reservations/search', [ReservationController::class, 'search'])->name('api.reservations.search');
    Route::post('/reservations/{reservation}/checkin', [ReservationController::class, 'checkin'])->name('api.reservations.checkin');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('api.reservations.cancel');
});

// Authenticated Customer Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservation', [ReservationController::class, 'create'])->name('reservation.create');
    Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
    Route::get('/reservations/track', [ReservationController::class, 'track'])->name('reservations.track');

    //profile
    Route::get('/profile', [CustomerController::class, 'profile'])->name('customer.profile');
    Route::put('/profile', [CustomerController::class, 'update'])->name('customer.update');
});


// Route cho Admin và Manager
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    // Users
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}/update', [UserController::class, 'update'])->name('admin.users.update');

    // Employees
    Route::get('/employees', [EmployeeController::class, 'index'])->name('admin.employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('admin.employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('admin.employees.store');
    Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('admin.employees.show');
    Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('admin.employees.edit');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('admin.employees.update');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('admin.employees.destroy');

    // Combos
    Route::get('/combos', [ComboController::class, 'index'])->name('admin.combos.index');
    Route::get('/combos/create', [ComboController::class, 'create'])->name('admin.combos.create');
    Route::post('/combos', [ComboController::class, 'store'])->name('admin.combos.store');
    Route::get('/combos/{id}', [ComboController::class, 'show'])->name('admin.combos.show');
    Route::get('/combos/{id}/edit', [ComboController::class, 'edit'])->name('admin.combos.edit');
    Route::put('/combos/{id}', [ComboController::class, 'update'])->name('admin.combos.update');
    Route::delete('/combos/{id}', [ComboController::class, 'destroy'])->name('admin.combos.destroy');
    Route::get('/combos/trash', [ComboController::class, 'trash'])->name('admin.combos.trash');
    Route::post('/combos/restore/{id}', [ComboController::class, 'restore'])->name('admin.combos.restore');
    Route::delete('/combos/force-delete/{id}', [ComboController::class, 'forceDelete'])->name('admin.combos.forceDelete');


    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');

    // Tables
    Route::get('/tables', [TableController::class, 'index'])->name('admin.tables.index');
    Route::get('tables/create', [TableController::class, 'create'])->name('admin.tables.create');
    Route::post('/tables', [TableController::class, 'store'])->name('admin.tables.store');
    Route::delete('/tables/{id}', [TableController::class, 'destroy'])->name('admin.tables.destroy');
    Route::get('/tables/trashed', [TableController::class, 'trashed'])->name('admin.tables.trashed');
    Route::post('/tables/{id}/restore', [TableController::class, 'restore'])->name('admin.tables.restore');
    Route::delete('/tables/{id}/force-delete', [TableController::class, 'forceDelete'])->name('admin.tables.forceDelete');
    Route::get('/tables/{id}/edit', [TableController::class, 'edit'])->name('admin.tables.edit');
    Route::put('/tables/{id}', [TableController::class, 'update'])->name('admin.tables.update');


    // Detail Table

    Route::get('/tables/{id}/detail', [TableController::class, 'showDetail'])->name('admin.tables.detail');
    Route::post('/bills/create', [BillController::class, 'createBill'])->name('bills.create');
    Route::post('/bills/{id}/add-combo', [BillController::class, 'addComboToBill'])->name('bills.add-combo');
    Route::post('/bills/{id}/add-product', [BillController::class, 'addProductToBill'])->name('bills.add-product');
    Route::post('/bills/{id}/switch-regular', [BillController::class, 'switchToRegularTime'])->name('bills.switch-regular');
    Route::post('/bills/{id}/extend-combo', [BillController::class, 'extendComboTime'])->name('bills.extend-combo');
    Route::post('/bills/{id}/process-payment', [BillController::class, 'processPayment'])->name('bills.process-payment');
    Route::get('/bills/{id}/payment', [BillController::class, 'showPayment'])->name('bills.payment-page');
    Route::post('/bills/{id}/update-total', [BillController::class, 'updateBillTotal'])->name('bills.update-total');
    Route::post('/bills/quick-create', [BillController::class, 'createQuickBill'])->name('bills.quick-create');
    Route::post('/bills/{id}/convert-to-quick', [BillController::class, 'convertToQuick'])->name('bills.convert-to-quick');
    Route::post('/bills/{id}/start-playing', [BillController::class, 'startPlaying'])->name('bills.start-playing');

    Route::post('/bills/{id}/pause', [BillController::class, 'pauseTime'])->name('bills.pause');
    Route::post('/bills/{id}/resume', [BillController::class, 'resumeTime'])->name('bills.resume');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/trashed', [ProductController::class, 'trashed'])->name('admin.products.trashed');
    Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('admin.products.restore');
    Route::delete('/products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('admin.products.forceDelete');
    Route::get('/products/deleted', [ProductController::class, 'trashed'])->name('admin.products.deleted');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('admin.products.show');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');


    // Shift
    Route::get('/shifts', [ShiftController::class, 'index'])->name('admin.shifts.index');
    Route::get('/shifts/create', [ShiftController::class, 'create'])->name('admin.shifts.create');
    Route::post('/shifts/store', [ShiftController::class, 'store'])->name('admin.shifts.store');
    Route::get('/shifts/{id}/edit', [ShiftController::class, 'edit'])->name('admin.shifts.edit');
    Route::put('/shifts/{id}/update', [ShiftController::class, 'update'])->name('admin.shifts.update');

    // Shift Employee
    Route::get('/shift-employee', [ShiftController::class, 'shiftEmployee'])->name('admin.shiftEmployee.index');
    Route::post('/shiftE/schedule', [ShiftController::class, 'scheduleShifts'])->name('admin.shiftEmployee.schedule');
    Route::post('/shiftE/save-weekly', [ShiftController::class, 'saveWeeklySchedule'])->name('admin.shiftEmployee.saveWeekly');
    Route::post('/shiftE/bulk-schedule', [ShiftController::class, 'bulkScheduleShifts'])->name('admin.shiftEmployee.bulkSchedule');

    // Promotions
    Route::get('/promotions', [PromotionController::class, 'index'])->name('admin.promotions.index');
    Route::get('/promotions/create', [PromotionController::class, 'create'])->name('admin.promotions.create');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('admin.promotions.store');
    Route::get('/admin/promotions/{id}', [PromotionController::class, 'show'])->name('admin.promotions.show');



    Route::get('/promotions/{id}/edit', [PromotionController::class, 'edit'])->name('admin.promotions.edit');
    Route::put('/promotions/{id}/update', [PromotionController::class, 'update'])->name('admin.promotions.update');
    Route::get('/promotions/trashed', [PromotionController::class, 'trashed'])->name('admin.promotions.trashed');
    Route::post('/promotions/{id}/restore', [PromotionController::class, 'restore'])->name('admin.promotions.restore');
    Route::delete('/promotions/{id}/destroy', [PromotionController::class, 'destroy'])->name('admin.promotions.destroy');
    Route::delete('/promotions/{id}/force-delete', [PromotionController::class, 'forceDelete'])->name('admin.promotions.forceDelete');

    // Customers

    Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('admin.customers.show');
    Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');
    Route::get('/customers/trashed', [CustomerController::class, 'trash'])->name('admin.customers.trashed');
    Route::post('/customers/{id}/restore', [CustomerController::class, 'restore'])->name('admin.customers.restore');
    Route::delete('/customers/{id}/force-delete', [CustomerController::class, 'forceDelete'])->name('admin.customers.force-delete');
    //Combos - API routes (THÊM MỚI)
    Route::get('/combos-api/rates-by-category', [ComboController::class, 'getTableRatesByCategory'])->name('admin.combos.rates-by-category');
    Route::post('/combos-api/preview-price', [ComboController::class, 'previewComboPrice'])->name('admin.combos.preview-price');
    Route::get('/combos-api/calculate-table-price', [ComboController::class, 'calculateTablePriceAPI'])->name('admin.combos.calculate-table-price');

    // Category Routes
    Route::resource('categories', CategoryController::class, ['as' => 'admin']);
});
Route::get('/test-timezone', function () {
    return response()->json([
        'timezone' => config('app.timezone'),
        'current_time' => now()->format('Y-m-d H:i:s'),
        'php_version' => PHP_VERSION,
        'should_be_vietnam_time' => 'Yes - UTC+7'
    ]);
});
