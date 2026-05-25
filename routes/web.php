<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
        Route::get('/finances', [App\Http\Controllers\FinanceController::class, 'index'])->name('admin.finances.index');
        Route::post('/finances', [App\Http\Controllers\FinanceController::class, 'store'])->name('admin.finances.store');
        Route::delete('/finances/{finance}', [App\Http\Controllers\FinanceController::class, 'destroy'])->name('admin.finances.destroy');
        
        Route::get('/employees', [App\Http\Controllers\Admin\EmployeeManagementController::class, 'index'])->name('admin.employees.index');
        Route::get('/employees/attendance', [App\Http\Controllers\Admin\EmployeeManagementController::class, 'attendance'])->name('admin.employees.attendance');
        Route::post('/employees', [App\Http\Controllers\Admin\EmployeeManagementController::class, 'store'])->name('admin.employees.store');
        Route::put('/employees/{employee}', [App\Http\Controllers\Admin\EmployeeManagementController::class, 'update'])->name('admin.employees.update');
        Route::delete('/employees/{employee}', [App\Http\Controllers\Admin\EmployeeManagementController::class, 'destroy'])->name('admin.employees.destroy');
        
        Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('admin.attendance.index');
        Route::get('/loans', [App\Http\Controllers\LoanController::class, 'index'])->name('admin.loans.index');
        Route::patch('/loans/{loan}', [App\Http\Controllers\LoanController::class, 'updateStatus'])->name('admin.loans.update');
        
        Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/reports/export/excel', [App\Http\Controllers\ReportController::class, 'exportExcel'])->name('admin.reports.export.excel');
        
        Route::get('/testimonials', [App\Http\Controllers\Admin\TestimonialController::class, 'index'])->name('admin.testimonials.index');
        
        Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
        Route::post('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');
    });

    Route::middleware(['role:employee'])->prefix('employee')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'employee'])->name('employee.dashboard');
        Route::get('/orders', [App\Http\Controllers\OrderController::class, 'employeeIndex'])->name('employee.orders.index');
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
    });

    // Notifications (Shared for all roles)
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications/recent', [App\Http\Controllers\NotificationController::class, 'getRecent'])->name('notifications.recent');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::middleware(['role:customer', 'phone_complete'])->prefix('customer')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'customer'])->name('customer.dashboard');
        Route::get('/services', [App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
        Route::get('/my-orders', [App\Http\Controllers\OrderController::class, 'myOrders'])->name('orders.my-orders');
        Route::get('/history', [App\Http\Controllers\OrderController::class, 'history'])->name('orders.history');
        Route::get('/orders/create', [App\Http\Controllers\OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
        Route::post('/orders/{order}/review', [App\Http\Controllers\OrderController::class, 'submitReview'])->name('orders.review.submit');
    });

    Route::get('/customer/orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::get('/customer/orders/{order}/receipt', [App\Http\Controllers\OrderController::class, 'receipt'])->name('orders.receipt');
    Route::post('/midtrans/callback', [App\Http\Controllers\MidtransController::class, 'callback'])->name('midtrans.callback');
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
