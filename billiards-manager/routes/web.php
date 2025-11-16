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
use App\Http\Controllers\MockPaymentController;
use App\Http\Controllers\BillTimeUsageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerBillController;
use App\Http\Controllers\Api\ComboTimeController;
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

    Route::middleware(['auth'])->group(function () {
        Route::post('/reservations/search', [ReservationController::class, 'search'])->name('api.reservations.search');
        Route::post('/reservations/{reservation}/checkin', [ReservationController::class, 'checkin'])->name('api.reservations.checkin');
        Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('api.reservations.cancel');
    });
});

// Authenticated Customer Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservation', [ReservationController::class, 'create'])->name('reservation.create');
    Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::get('/reservations/track', [ReservationController::class, 'track'])->name('reservations.track');

    //profile
    // Route::get('/profile', [CustomerController::class, 'profile'])->name('customer.profile');
    // Route::put('/profile', [CustomerController::class, 'update'])->name('customer.update');
});


// Route cho Admin và Manager
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');    // Users
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
    Route::get('tables/{id}', [TableController::class, 'detail'])->name('admin.tables.detail');
    // Mở bàn từ giao diện admin
    Route::post('tables/{id}/open', [BillController::class, 'openTable'])->name('tables.open');
    // Tạm dừng
    Route::post('/bills/{bill}/pause', [BillController::class, 'pauseTable'])->name('bills.pause');
    Route::post('/bills/{bill}/resume', [BillController::class, 'resumeTable'])->name('bills.resume');
    Route::post('/bills/{bill}/close', [BillController::class, 'closeTable'])->name('bills.close');
    // Thêm sản phẩm vào bill
    Route::post('bills/{bill}/product', [BillController::class, 'addProduct'])->name('bills.add-product');
    // Thêm combo vào bill
    Route::post('bills/{bill}/combo', [BillController::class, 'addCombo'])->name('bills.add-combo');


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

    //Combos - API routes (THÊM MỚI)
    Route::get('/combos-api/rates-by-category', [ComboController::class, 'getTableRatesByCategory'])->name('admin.combos.rates-by-category');
    Route::post('/combos-api/preview-price', [ComboController::class, 'previewComboPrice'])->name('admin.combos.preview-price');
    Route::get('/combos-api/calculate-table-price', [ComboController::class, 'calculateTablePriceAPI'])->name('admin.combos.calculate-table-price');

    Route::prefix('reservations')->name('admin.reservations.')->group(function () {
        Route::get('/', [ReservationController::class, 'adminIndex'])->name('index');
        Route::post('/', [ReservationController::class, 'store'])->name('store');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
        Route::post('/{reservation}/checkin', [ReservationController::class, 'checkin'])->name('checkin');
        Route::post('/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('cancel');
    });

    // Payment routes
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/reservations/{reservation}/payment', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
    Route::post('/payments/{payment}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');
});

// Thêm vào phần authenticated customer routes
Route::middleware(['auth'])->group(function () {
    // Customer reservation management
    Route::get('/reservations', [ReservationController::class, 'track'])->name('reservations.index');
    Route::get('/reservations/track', [ReservationController::class, 'track'])->name('reservations.track');
    Route::get('/reservation/create', [ReservationController::class, 'create'])->name('reservation.create');
    Route::post('/reservation', [ReservationController::class, 'store'])->name('reservations.store');

    // Payment routes for customers
    Route::get('/reservation/{id}/payment', [ReservationController::class, 'showPayment'])->name('reservation.payment');
    Route::post('/reservation/{id}/payment', [ReservationController::class, 'processPayment'])->name('reservation.process-payment');
});

Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {

    // Lịch sử hóa đơn
    Route::get('/bills', [CustomerBillController::class, 'index'])->name('bills.index');
    Route::get('/bills/{id}', [CustomerBillController::class, 'show'])->name('bills.show');
    Route::get('/bills/{id}/edit', [CustomerBillController::class, 'requestEdit'])->name('bills.edit');

    // API cho khách hàng
    Route::get('/api/bills', [CustomerBillController::class, 'apiList'])->name('bills.api');

    // Xuất PDF (tùy chọn)
    Route::get('/bills/{id}/pdf', [CustomerBillController::class, 'exportPdf'])->name('bills.pdf');
});

// ========== CẬP NHẬT API ROUTES ==========
Route::prefix('api')->group(function () {
    Route::post('/tables/available', [ReservationController::class, 'checkAvailability'])->name('api.tables.available');
    Route::post('/api/tables/available', [ReservationController::class, 'checkAvailability'])->name('api.tables.available');
    Route::post('/reservations/search', [ReservationController::class, 'search'])->name('api.reservations.search');
    Route::post('/reservations/{reservation}/checkin', [ReservationController::class, 'checkin'])->name('api.reservations.checkin');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('api.reservations.cancel');

    // *** MỚI: API sửa bill detail ***
    Route::post('/bills/{bill}/details', [BillController::class, 'addProduct'])->name('api.bills.add-product');
    Route::put('/bills/{bill}/details/{detail}', [BillController::class, 'updateBillDetail'])->name('api.bills.update-detail');
    Route::delete('/bills/{bill}/details/{detail}', [BillController::class, 'deleteBillDetail'])->name('api.bills.delete-detail');
});

Route::prefix('mock-payment')->name('mock.payment.')->group(function () {
    Route::get('/form', [MockPaymentController::class, 'showPaymentForm'])->name('form');
    Route::post('/process', [MockPaymentController::class, 'processPayment'])->name('process');

    // Tạo thanh toán cho reservation
    Route::post('/reservation/{reservation}/create', [MockPaymentController::class, 'createReservationPayment'])
        ->name('reservation.create');

    // Tạo thanh toán cho bill
    Route::post('/bill/{bill}/create', [MockPaymentController::class, 'createBillPayment'])
        ->name('bill.create');

    // Thông tin chuyển khoản
    Route::get('/bank-transfer/{transaction_id}', [MockPaymentController::class, 'showBankTransfer'])
        ->name('bank-transfer');
});

// ========== CẬP NHẬT PAYMENT ROUTES (SỬ DỤNG MOCK) ==========
Route::prefix('payment')->name('payment.')->group(function () {
    // Reservation payment - SỬ DỤNG MOCK
    Route::post('/reservation/{reservation}/create', [MockPaymentController::class, 'createReservationPayment'])
        ->name('reservation.create');

    // Bill payment - SỬ DỤNG MOCK
    Route::post('/bill/{bill}/create', [MockPaymentController::class, 'createBillPayment'])
        ->name('bill.create');

    // Callback routes (giữ nguyên cấu trúc)
    Route::get('/reservation/callback', [MockPaymentController::class, 'processPayment'])
        ->name('reservation.callback');
    Route::get('/bill/callback', [MockPaymentController::class, 'processPayment'])
        ->name('bill.callback');
});

Route::get('/track-reservation', [ReservationController::class, 'track'])
    ->name('reservations.track');

Route::post('/api/reservations/search', [ReservationController::class, 'search'])
    ->name('api.reservations.search');

// Customer Reservation Routes (Authenticated or Guest with code)
Route::middleware(['web'])->group(function () {
    // Create reservation
    Route::get('/reservations/create', [ReservationController::class, 'create'])
        ->name('reservation.create');
    
    Route::post('/reservations', [ReservationController::class, 'store'])
        ->name('reservations.store');

    // View reservation (public with reservation ID)
    Route::get('/reservations/{id}', [ReservationController::class, 'show'])
        ->name('reservations.show');

    // Payment
    Route::get('/reservations/{id}/payment', [ReservationController::class, 'showPayment'])
        ->name('reservations.payment');
    
    Route::post('/reservations/{id}/payment', [ReservationController::class, 'processPayment'])
        ->name('reservation.process-payment');
});

// API Routes (for AJAX calls)
Route::prefix('api')->group(function () {
    // Check table availability
    Route::post('/tables/available', [TableController::class, 'checkAvailability'])
        ->name('api.tables.available');

    // Check-in
    Route::post('/reservations/{id}/checkin', [ReservationController::class, 'checkin'])
        ->name('api.reservations.checkin');

    // Cancel reservation
    Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancel'])
        ->name('api.reservations.cancel');
});

// Mock Payment Routes
Route::prefix('mock-payment')->name('mock.')->group(function () {
    Route::get('/form', [MockPaymentController::class, 'showPaymentForm'])
        ->name('payment.form');
    
    Route::post('/process', [MockPaymentController::class, 'processPayment'])
        ->name('payment.process');
});

// Admin Routes (Protected by auth and role middleware)
Route::middleware(['auth', 'role:admin,staff'])->prefix('admin')->name('admin.')->group(function () {
    // Reservation management
    Route::get('/reservations', [ReservationController::class, 'adminIndex'])
        ->name('reservations.index');
    
    Route::get('/reservations/{id}', [ReservationController::class, 'show'])
        ->name('reservations.show');
});
