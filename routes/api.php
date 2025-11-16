<?php

declare(strict_types=1);

use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BranchController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\EmployeeShiftController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\InventoryMovementController;
use App\Http\Controllers\API\NotificationTemplateController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\ServiceCategoryController;
use App\Http\Controllers\API\ServiceController;
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
    });
});
