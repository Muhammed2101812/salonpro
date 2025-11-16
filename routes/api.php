<?php

declare(strict_types=1);

use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\AppointmentHistoryController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BranchController;
use App\Http\Controllers\API\CouponController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\EmployeeShiftController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\InventoryMovementController;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\LoyaltyPointController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\NotificationTemplateController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProductVariantController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\ServiceCategoryController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\ServiceReviewController;
use App\Http\Controllers\API\SettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    // Auth routes (public)
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        // Auth
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index']);

        // Branches
        Route::apiResource('branches', BranchController::class);
        Route::post('branches/{branch}/restore', [BranchController::class, 'restore'])->name('branches.restore');
        Route::delete('branches/{branch}/force', [BranchController::class, 'forceDestroy'])->name('branches.force-destroy');

        // Customers
        Route::apiResource('customers', CustomerController::class);
        Route::get('customers/{customer}/timeline', [CustomerController::class, 'timeline'])->name('customers.timeline');
        Route::get('customers/{customer}/stats', [CustomerController::class, 'stats'])->name('customers.stats');
        Route::post('customers/{customer}/restore', [CustomerController::class, 'restore'])->name('customers.restore');
        Route::delete('customers/{customer}/force', [CustomerController::class, 'forceDestroy'])->name('customers.force-destroy');

        // Employees
        Route::apiResource('employees', EmployeeController::class);
        Route::post('employees/{employee}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
        Route::delete('employees/{employee}/force', [EmployeeController::class, 'forceDestroy'])->name('employees.force-destroy');

        // Employee Shifts
        Route::apiResource('employee-shifts', EmployeeShiftController::class);
        Route::post('employee-shifts/{employee_shift}/restore', [EmployeeShiftController::class, 'restore'])->name('employee-shifts.restore');
        Route::delete('employee-shifts/{employee_shift}/force', [EmployeeShiftController::class, 'forceDestroy'])->name('employee-shifts.force-destroy');

        // Products
        Route::apiResource('products', ProductController::class);
        Route::post('products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('products/{product}/force', [ProductController::class, 'forceDestroy'])->name('products.force-destroy');

        // Inventory Movements
        Route::apiResource('inventory-movements', InventoryMovementController::class);

        // Expenses
        Route::apiResource('expenses', ExpenseController::class);

        // Payments
        Route::apiResource('payments', PaymentController::class);
        Route::post('payments/{payment}/restore', [PaymentController::class, 'restore'])->name('payments.restore');
        Route::delete('payments/{payment}/force', [PaymentController::class, 'forceDestroy'])->name('payments.force-destroy');

        // Sales
        Route::apiResource('sales', SaleController::class);

        // Settings
        Route::apiResource('settings', SettingController::class)->only(['index', 'store', 'update']);

        // Service Categories
        Route::apiResource('service-categories', ServiceCategoryController::class);
        Route::post('service-categories/{service_category}/restore', [ServiceCategoryController::class, 'restore'])->name('service-categories.restore');
        Route::delete('service-categories/{service_category}/force', [ServiceCategoryController::class, 'forceDestroy'])->name('service-categories.force-destroy');

        // Services
        Route::apiResource('services', ServiceController::class);
        Route::post('services/{service}/restore', [ServiceController::class, 'restore'])->name('services.restore');
        Route::delete('services/{service}/force', [ServiceController::class, 'forceDestroy'])->name('services.force-destroy');

        // Appointments
        Route::apiResource('appointments', AppointmentController::class);
        Route::post('appointments/{appointment}/restore', [AppointmentController::class, 'restore'])->name('appointments.restore');
        Route::delete('appointments/{appointment}/force', [AppointmentController::class, 'forceDestroy'])->name('appointments.force-destroy');

        // Notification Templates
        Route::apiResource('notification-templates', NotificationTemplateController::class);
        Route::post('notification-templates/{notification_template}/restore', [NotificationTemplateController::class, 'restore'])->name('notification-templates.restore');
        Route::delete('notification-templates/{notification_template}/force', [NotificationTemplateController::class, 'forceDestroy'])->name('notification-templates.force-destroy');

        // Invoices
        Route::apiResource('invoices', InvoiceController::class);
        Route::post('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
        Route::post('invoices/{invoice}/mark-as-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-as-paid');
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf'])->name('invoices.pdf');
        Route::post('invoices/{invoice}/send-email', [InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
        Route::get('invoices-stats', [InvoiceController::class, 'stats'])->name('invoices.stats');

        // Loyalty Points
        Route::get('loyalty-points/customers/{customer}/balance', [LoyaltyPointController::class, 'balance'])->name('loyalty-points.balance');
        Route::get('loyalty-points/customers/{customer}/history', [LoyaltyPointController::class, 'history'])->name('loyalty-points.history');
        Route::post('loyalty-points/customers/{customer}/award', [LoyaltyPointController::class, 'award'])->name('loyalty-points.award');
        Route::post('loyalty-points/customers/{customer}/redeem', [LoyaltyPointController::class, 'redeem'])->name('loyalty-points.redeem');
        Route::get('loyalty-points/customers/{customer}/expiring', [LoyaltyPointController::class, 'expiringPoints'])->name('loyalty-points.expiring');
        Route::post('loyalty-points/calculate', [LoyaltyPointController::class, 'calculatePoints'])->name('loyalty-points.calculate');

        // Product Variants
        Route::apiResource('product-variants', ProductVariantController::class);
        Route::get('product-variants/sku/{sku}', [ProductVariantController::class, 'findBySku'])->name('product-variants.find-by-sku');
        Route::get('product-variants/barcode/{barcode}', [ProductVariantController::class, 'findByBarcode'])->name('product-variants.find-by-barcode');
        Route::post('product-variants/{variant}/update-stock', [ProductVariantController::class, 'updateStock'])->name('product-variants.update-stock');
        Route::post('product-variants/{variant}/check-stock', [ProductVariantController::class, 'checkStock'])->name('product-variants.check-stock');

        // Coupons
        Route::post('coupons/validate', [CouponController::class, 'validate'])->name('coupons.validate');
        Route::post('coupons/apply', [CouponController::class, 'apply'])->name('coupons.apply');
        Route::get('coupons/{coupon}/usage', [CouponController::class, 'usage'])->name('coupons.usage');
        Route::get('coupons/customers/{customer}/usage', [CouponController::class, 'customerUsage'])->name('coupons.customer-usage');
        Route::post('coupons/calculate-discount', [CouponController::class, 'calculateDiscount'])->name('coupons.calculate-discount');

        // Service Reviews
        Route::apiResource('service-reviews', ServiceReviewController::class);
        Route::post('service-reviews/{review}/approve', [ServiceReviewController::class, 'approve'])->name('service-reviews.approve');
        Route::post('service-reviews/{review}/reject', [ServiceReviewController::class, 'reject'])->name('service-reviews.reject');
        Route::get('services/{service}/reviews/published', [ServiceReviewController::class, 'published'])->name('service-reviews.published');
        Route::get('services/{service}/reviews/average-rating', [ServiceReviewController::class, 'averageRating'])->name('service-reviews.average-rating');

        // Notifications
        Route::apiResource('notifications', NotificationController::class)->only(['index', 'store', 'show']);
        Route::post('notifications/{notification}/send', [NotificationController::class, 'send'])->name('notifications.send');
        Route::post('notifications/process-pending', [NotificationController::class, 'processPending'])->name('notifications.process-pending');
        Route::get('notifications/pending/count', [NotificationController::class, 'pendingCount'])->name('notifications.pending-count');
        Route::post('notifications/{notification}/retry', [NotificationController::class, 'retry'])->name('notifications.retry');

        // Appointment History
        Route::apiResource('appointment-history', AppointmentHistoryController::class)->only(['index', 'store', 'show']);
        Route::get('appointment-history/recent', [AppointmentHistoryController::class, 'recentChanges'])->name('appointment-history.recent');
        Route::get('appointments/{appointment}/history', [AppointmentHistoryController::class, 'appointmentHistory'])->name('appointment-history.appointment');
        Route::get('users/{user}/appointment-changes', [AppointmentHistoryController::class, 'userChanges'])->name('appointment-history.user');
    });
});
