<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Helper route to create storage link on production server
Route::get('/storage-link', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        return 'Storage link created successfully!';
    } catch (\Exception $e) {
        return 'Failed to create link: ' . $e->getMessage();
    }
});

Route::get('auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback']);

// OTP Password Reset Routes
Route::get('forgot-password', [App\Http\Controllers\Auth\OTPPasswordController::class, 'showRequestForm'])->name('password.request');
Route::post('forgot-password', [App\Http\Controllers\Auth\OTPPasswordController::class, 'sendOTP'])->name('password.otp.send');
Route::get('verify-otp', [App\Http\Controllers\Auth\OTPPasswordController::class, 'showVerifyForm'])->name('password.otp.verify');
Route::post('verify-otp', [App\Http\Controllers\Auth\OTPPasswordController::class, 'verifyOTP'])->name('password.otp.verify.post');
Route::post('reset-password-otp', [App\Http\Controllers\Auth\OTPPasswordController::class, 'resetPassword'])->name('password.otp.reset');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'admin'])->name('admin.dashboard');
        Route::get('/services', [App\Http\Controllers\ServiceController::class, 'adminIndex'])->name('admin.services.index');
        Route::post('/services', [App\Http\Controllers\ServiceController::class, 'store'])->name('admin.services.store');
        Route::put('/services/{service}', [App\Http\Controllers\ServiceController::class, 'update'])->name('admin.services.update');
        Route::delete('/services/{service}', [App\Http\Controllers\ServiceController::class, 'destroy'])->name('admin.services.destroy');
        Route::get('/orders', [App\Http\Controllers\OrderController::class, 'adminIndex'])->name('admin.orders.index');
        Route::post('/orders', [App\Http\Controllers\OrderController::class, 'adminStore'])->name('admin.orders.store');
        Route::put('/orders/{order}', [App\Http\Controllers\OrderController::class, 'adminUpdate'])->name('admin.orders.update');
        Route::patch('/orders/{order}/status', [App\Http\Controllers\OrderController::class, 'updateStatus'])->name('admin.orders.status.update');
        Route::delete('/orders/{order}', [App\Http\Controllers\OrderController::class, 'adminDestroy'])->name('admin.orders.destroy');
        Route::get('/finances', [App\Http\Controllers\FinanceController::class, 'index'])->name('admin.finances.index');
        Route::post('/finances', [App\Http\Controllers\FinanceController::class, 'store'])->name('admin.finances.store');
        Route::delete('/finances/{finance}', [App\Http\Controllers\FinanceController::class, 'destroy'])->name('admin.finances.destroy');
        Route::get('/finances/export/cashbook', [App\Http\Controllers\FinanceController::class, 'exportCashbookExcel'])->name('admin.finances.export.cashbook');
        
        Route::get('/employees', [App\Http\Controllers\Admin\EmployeeManagementController::class, 'index'])->name('admin.employees.index');
        Route::get('/employees/attendance', [App\Http\Controllers\Admin\EmployeeManagementController::class, 'attendance'])->name('admin.employees.attendance');
        Route::post('/employees', [App\Http\Controllers\Admin\EmployeeManagementController::class, 'store'])->name('admin.employees.store');
        Route::put('/employees/{employee}', [App\Http\Controllers\Admin\EmployeeManagementController::class, 'update'])->name('admin.employees.update');
        Route::delete('/employees/{employee}', [App\Http\Controllers\Admin\EmployeeManagementController::class, 'destroy'])->name('admin.employees.destroy');
        
        // Customers
        Route::get('/customers', [App\Http\Controllers\Admin\CustomerManagementController::class, 'index'])->name('admin.customers.index');
        Route::post('/customers', [App\Http\Controllers\Admin\CustomerManagementController::class, 'store'])->name('admin.customers.store');
        Route::put('/customers/{customer}', [App\Http\Controllers\Admin\CustomerManagementController::class, 'update'])->name('admin.customers.update');
        Route::delete('/customers/{customer}', [App\Http\Controllers\Admin\CustomerManagementController::class, 'destroy'])->name('admin.customers.destroy');
        
        Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('admin.attendance.index');
        Route::get('/loans', [App\Http\Controllers\LoanController::class, 'index'])->name('admin.loans.index');
        Route::patch('/loans/{loan}', [App\Http\Controllers\LoanController::class, 'updateStatus'])->name('admin.loans.update');
        // Laporan Operasional
        Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/reports/export/excel', [App\Http\Controllers\ReportController::class, 'exportExcel'])->name('admin.reports.export.excel');
        Route::get('/reports/export/revenue/excel', [App\Http\Controllers\ReportController::class, 'exportRevenueExcel'])->name('admin.reports.export.revenue.excel');
        Route::get('/reports/export/revenue/pdf', [App\Http\Controllers\ReportController::class, 'exportRevenuePdf'])->name('admin.reports.export.revenue.pdf');
        
        // Settings
        Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('admin.settings.index');
        Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('admin.settings.update');
        Route::post('/settings/admin', [App\Http\Controllers\SettingsController::class, 'updateAdmin'])->name('admin.settings.update-admin');
        
        Route::get('/testimonials', [App\Http\Controllers\Admin\TestimonialController::class, 'index'])->name('admin.testimonials.index');
        
        // Inventories
        Route::resource('inventories', \App\Http\Controllers\Admin\InventoryController::class)->except(['create', 'edit', 'show'])->names([
            'index' => 'admin.inventories.index',
            'store' => 'admin.inventories.store',
            'update' => 'admin.inventories.update',
            'destroy' => 'admin.inventories.destroy',
        ]);

    });

    Route::middleware(['role:employee'])->prefix('employee')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'employee'])->name('employee.dashboard');
        Route::get('/orders', [App\Http\Controllers\OrderController::class, 'employeeIndex'])->name('employee.orders.index');
        Route::post('/orders', [App\Http\Controllers\OrderController::class, 'employeeStore'])->name('employee.orders.store');
        Route::put('/orders/{order}', [App\Http\Controllers\OrderController::class, 'adminUpdate'])->name('employee.orders.update');
        Route::patch('/orders/{order}', [App\Http\Controllers\OrderController::class, 'updateStatus'])->name('orders.status.update');
        
        Route::post('/attendance/clock-in', [App\Http\Controllers\AttendanceController::class, 'clockIn'])->name('employee.attendance.clock-in');
        Route::post('/attendance/clock-out', [App\Http\Controllers\AttendanceController::class, 'clockOut'])->name('employee.attendance.clock-out');
        
        Route::get('/orders/scan', [App\Http\Controllers\OrderController::class, 'scan'])->name('employee.orders.scan');
        Route::post('/orders/scan/process', [App\Http\Controllers\OrderController::class, 'processScan'])->name('employee.orders.scan.process');
        
        Route::get('/loans', [App\Http\Controllers\LoanController::class, 'index'])->name('employee.loans.index');
        Route::post('/loans', [App\Http\Controllers\LoanController::class, 'store'])->name('employee.loans.store');
        
        Route::get('/reports', [App\Http\Controllers\ReportController::class, 'employeeIndex'])->name('employee.reports.index');
        Route::get('/reports/attendance/excel', [App\Http\Controllers\ReportController::class, 'exportAttendanceExcel'])->name('employee.reports.attendance.excel');
        Route::get('/reports/attendance/pdf', [App\Http\Controllers\ReportController::class, 'exportAttendancePdf'])->name('employee.reports.attendance.pdf');
        Route::post('/orders/{order}/confirm-payment', [App\Http\Controllers\OrderController::class, 'confirmPayment'])->name('orders.payment.confirm');
        Route::post('/orders/{order}/remind-payment', [App\Http\Controllers\OrderController::class, 'remindPayment'])->name('orders.payment.remind');

        // Inventories
        Route::resource('inventories', \App\Http\Controllers\Employee\InventoryController::class)->except(['create', 'edit', 'show'])->names([
            'index' => 'employee.inventories.index',
            'store' => 'employee.inventories.store',
            'update' => 'employee.inventories.update',
            'destroy' => 'employee.inventories.destroy',
        ]);
    });

    // Notifications (Shared for all roles)
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications/recent', [App\Http\Controllers\NotificationController::class, 'getRecent'])->middleware('throttle:15,1')->name('notifications.recent');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::middleware(['role:customer', 'phone_complete'])->prefix('customer')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'customer'])->name('customer.dashboard');
        Route::get('/services', [App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
        
        // Cart Routes
        Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
        Route::delete('/cart/remove/{id}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
        Route::get('/checkout', [App\Http\Controllers\CartController::class, 'checkoutForm'])->name('orders.checkout');

        Route::get('/my-orders', [App\Http\Controllers\OrderController::class, 'myOrders'])->name('orders.my-orders');
        Route::get('/history', [App\Http\Controllers\OrderController::class, 'history'])->name('orders.history');
        Route::post('/orders/checkout', [App\Http\Controllers\OrderController::class, 'storeCheckout'])->name('orders.store_checkout');
        Route::post('/orders', [App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
        Route::post('/orders/{order}/review', [App\Http\Controllers\OrderController::class, 'submitReview'])->name('orders.review.submit');
        Route::post('/orders/{order}/cancel', [App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');
        
        // Address Book Routes
        Route::resource('addresses', \App\Http\Controllers\UserAddressController::class)->except(['show']);
        
        // Legacy Address Route (Redirect to new Address Book)
        Route::redirect('/address', '/customer/addresses')->name('address.edit');
        
        // Wilayah Indonesia APIs
        Route::get('/api/wilayah/provinces', [App\Http\Controllers\WilayahApiController::class, 'getProvinces'])->name('api.wilayah.provinces');
        Route::get('/api/wilayah/cities', [App\Http\Controllers\WilayahApiController::class, 'getCities'])->name('api.wilayah.cities');
        Route::get('/api/wilayah/districts', [App\Http\Controllers\WilayahApiController::class, 'getDistricts'])->name('api.wilayah.districts');
        Route::get('/api/wilayah/villages', [App\Http\Controllers\WilayahApiController::class, 'getVillages'])->name('api.wilayah.villages');
        

    });
    Route::get('/customer/orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::get('/customer/orders/{order}/receipt', [App\Http\Controllers\OrderController::class, 'receipt'])->name('orders.receipt');
    Route::post('/customer/orders/{order}/upload-payment-proof', [App\Http\Controllers\OrderController::class, 'uploadPaymentProof'])->name('orders.upload_payment_proof');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Outlet Quick Access (QR Scan)
Route::get('/outlet/scan', [App\Http\Controllers\Auth\OutletScanController::class, 'handleScan'])->name('outlet.scan');

// ========== ANTRIAN PUBLIK ==========
// Halaman display antrian (tanpa login - untuk TV/monitor di outlet)
Route::get('/antrian', [App\Http\Controllers\QueueController::class, 'display'])->name('queue.display');
Route::get('/api/antrian', [App\Http\Controllers\QueueController::class, 'getData'])->name('queue.data');
Route::get('/cek-antrian', [App\Http\Controllers\QueueController::class, 'check'])->name('queue.check');

require __DIR__.'/auth.php';
