<?php

declare(strict_types=1);

use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\CashRegisterController;
use App\Http\Controllers\Api\EmployeeAttendanceController;
use App\Http\Controllers\Api\EmployeeCommissionController;
use App\Http\Controllers\Api\EmployeeLeaveController;
use App\Http\Controllers\Api\IntegrationController;
use App\Http\Controllers\Api\ProductAttributeController;
use App\Http\Controllers\Api\ProductAttributeValueController;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\ReportExecutionController;
use App\Http\Controllers\Api\ReportTemplateController;
use App\Http\Controllers\Api\KpiDefinitionController;
use App\Http\Controllers\Api\StockAlertController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\WebhookLogController;
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
use App\Http\Controllers\API\StockTransferController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    // Auth routes (public)
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        // Auth
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);

        // User Branch Settings
        Route::get('user/current-branch', [AuthController::class, 'currentBranch'])->name('user.current-branch');
        Route::post('user/set-current-branch', [AuthController::class, 'setCurrentBranch'])->name('user.set-current-branch');

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

        // Customer Categories
        Route::apiResource('customer-categories', \App\Http\Controllers\API\CustomerCategoryController::class);

        // Customer Tags
        Route::apiResource('customer-tags', \App\Http\Controllers\API\CustomerTagController::class);

        // Customer Segments
        Route::apiResource('customer-segments', \App\Http\Controllers\API\CustomerSegmentController::class);

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
        Route::post('coupons/validate', [CouponController::class, 'validateCoupon'])->name('coupons.validate');
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

        // Stock Transfers
        Route::apiResource('stock-transfers', StockTransferController::class);
        Route::post('stock-transfers/{transfer}/approve', [StockTransferController::class, 'approve'])->name('stock-transfers.approve');
        Route::post('stock-transfers/{transfer}/reject', [StockTransferController::class, 'reject'])->name('stock-transfers.reject');
        Route::post('stock-transfers/{transfer}/complete', [StockTransferController::class, 'complete'])->name('stock-transfers.complete');
        Route::post('stock-transfers/{transfer}/cancel', [StockTransferController::class, 'cancel'])->name('stock-transfers.cancel');

        // Suppliers
        Route::apiResource('suppliers', SupplierController::class);
        Route::get('suppliers/{supplier}/stats', [SupplierController::class, 'stats'])->name('suppliers.stats');
        Route::post('suppliers/{supplier}/activate', [SupplierController::class, 'activate'])->name('suppliers.activate');
        Route::post('suppliers/{supplier}/deactivate', [SupplierController::class, 'deactivate'])->name('suppliers.deactivate');

        // Purchase Orders
        Route::apiResource('purchase-orders', PurchaseOrderController::class);
        Route::get('purchase-orders-pending', [PurchaseOrderController::class, 'pending'])->name('purchase-orders.pending');
        Route::get('purchase-orders-overdue', [PurchaseOrderController::class, 'overdue'])->name('purchase-orders.overdue');
        Route::post('purchase-orders/{purchase_order}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
        Route::post('purchase-orders/{purchase_order}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
        Route::get('purchase-orders-totals', [PurchaseOrderController::class, 'totals'])->name('purchase-orders.totals');

        // Bank Accounts
        Route::apiResource('bank-accounts', BankAccountController::class);
        Route::post('bank-accounts/{bank_account}/deposit', [BankAccountController::class, 'deposit'])->name('bank-accounts.deposit');
        Route::post('bank-accounts/{bank_account}/withdraw', [BankAccountController::class, 'withdraw'])->name('bank-accounts.withdraw');
        Route::post('bank-accounts/{bank_account}/activate', [BankAccountController::class, 'activate'])->name('bank-accounts.activate');
        Route::post('bank-accounts/{bank_account}/deactivate', [BankAccountController::class, 'deactivate'])->name('bank-accounts.deactivate');
        Route::get('bank-accounts-total-balance', [BankAccountController::class, 'totalBalance'])->name('bank-accounts.total-balance');

        // Cash Registers
        Route::apiResource('cash-registers', CashRegisterController::class);
        Route::post('cash-registers/{cash_register}/add-cash', [CashRegisterController::class, 'addCash'])->name('cash-registers.add-cash');
        Route::post('cash-registers/{cash_register}/remove-cash', [CashRegisterController::class, 'removeCash'])->name('cash-registers.remove-cash');
        Route::post('cash-registers/{cash_register}/open', [CashRegisterController::class, 'open'])->name('cash-registers.open');
        Route::post('cash-registers/{cash_register}/close', [CashRegisterController::class, 'close'])->name('cash-registers.close');
        Route::get('cash-registers-total-balance', [CashRegisterController::class, 'totalBalance'])->name('cash-registers.total-balance');

        // Employee Attendance
        Route::get('employee-attendance-today', [EmployeeAttendanceController::class, 'today'])->name('employee-attendance.today');
        Route::get('employee-attendance-active', [EmployeeAttendanceController::class, 'active'])->name('employee-attendance.active');
        Route::post('employee-attendance-clock-in', [EmployeeAttendanceController::class, 'clockIn'])->name('employee-attendance.clock-in');
        Route::post('employee-attendance/{attendance}/clock-out', [EmployeeAttendanceController::class, 'clockOut'])->name('employee-attendance.clock-out');
        Route::post('employee-attendance/{attendance}/start-break', [EmployeeAttendanceController::class, 'startBreak'])->name('employee-attendance.start-break');
        Route::post('employee-attendance/{attendance}/end-break', [EmployeeAttendanceController::class, 'endBreak'])->name('employee-attendance.end-break');
        Route::get('employee-attendance-summary', [EmployeeAttendanceController::class, 'summary'])->name('employee-attendance.summary');
        Route::apiResource('employee-attendance', EmployeeAttendanceController::class)->except(['store']);

        // Employee Commissions
        Route::get('employee-commissions-unpaid', [EmployeeCommissionController::class, 'unpaid'])->name('employee-commissions.unpaid');
        Route::post('employee-commissions/{commission}/mark-as-paid', [EmployeeCommissionController::class, 'markAsPaid'])->name('employee-commissions.mark-as-paid');
        Route::post('employee-commissions-mark-multiple-as-paid', [EmployeeCommissionController::class, 'markMultipleAsPaid'])->name('employee-commissions.mark-multiple-as-paid');
        Route::get('employee-commissions-summary', [EmployeeCommissionController::class, 'summary'])->name('employee-commissions.summary');
        Route::post('employee-commissions-calculate', [EmployeeCommissionController::class, 'calculate'])->name('employee-commissions.calculate');
        Route::apiResource('employee-commissions', EmployeeCommissionController::class);

        // Employee Leaves
        Route::get('employee-leaves-pending', [EmployeeLeaveController::class, 'pending'])->name('employee-leaves.pending');
        Route::post('employee-leaves/{leave}/approve', [EmployeeLeaveController::class, 'approve'])->name('employee-leaves.approve');
        Route::post('employee-leaves/{leave}/reject', [EmployeeLeaveController::class, 'reject'])->name('employee-leaves.reject');
        Route::post('employee-leaves/{leave}/cancel', [EmployeeLeaveController::class, 'cancel'])->name('employee-leaves.cancel');
        Route::get('employee-leaves-summary', [EmployeeLeaveController::class, 'summary'])->name('employee-leaves.summary');
        Route::get('employee-leaves-check-overlapping', [EmployeeLeaveController::class, 'checkOverlapping'])->name('employee-leaves.check-overlapping');
        Route::apiResource('employee-leaves', EmployeeLeaveController::class);

        // Stock Alerts
        Route::get('stock-alerts-active', [StockAlertController::class, 'active'])->name('stock-alerts.active');
        Route::get('stock-alerts-resolved', [StockAlertController::class, 'resolved'])->name('stock-alerts.resolved');
        Route::get('stock-alerts-critical', [StockAlertController::class, 'critical'])->name('stock-alerts.critical');
        Route::post('stock-alerts/{stock_alert}/mark-as-notified', [StockAlertController::class, 'markAsNotified'])->name('stock-alerts.mark-as-notified');
        Route::post('stock-alerts/{stock_alert}/resolve', [StockAlertController::class, 'resolve'])->name('stock-alerts.resolve');
        Route::apiResource('stock-alerts', StockAlertController::class);

        // Product Attributes
        Route::get('product-attributes-filterable', [ProductAttributeController::class, 'filterable'])->name('product-attributes.filterable');
        Route::get('product-attributes-required', [ProductAttributeController::class, 'required'])->name('product-attributes.required');
        Route::get('product-attributes-sorted', [ProductAttributeController::class, 'sorted'])->name('product-attributes.sorted');
        Route::apiResource('product-attributes', ProductAttributeController::class);

        // Product Attribute Values
        Route::post('product-attribute-values-bulk-set', [ProductAttributeValueController::class, 'bulkSet'])->name('product-attribute-values.bulk-set');
        Route::delete('product-attribute-values-delete-attribute', [ProductAttributeValueController::class, 'deleteProductAttribute'])->name('product-attribute-values.delete-attribute');
        Route::apiResource('product-attribute-values', ProductAttributeValueController::class);

        // Report Templates
        Route::get('report-templates-active', [ReportTemplateController::class, 'active'])->name('report-templates.active');
        Route::get('report-templates-system', [ReportTemplateController::class, 'system'])->name('report-templates.system');
        Route::get('report-templates-user', [ReportTemplateController::class, 'user'])->name('report-templates.user');
        Route::post('report-templates/{report_template}/activate', [ReportTemplateController::class, 'activate'])->name('report-templates.activate');
        Route::post('report-templates/{report_template}/deactivate', [ReportTemplateController::class, 'deactivate'])->name('report-templates.deactivate');
        Route::apiResource('report-templates', ReportTemplateController::class);

        // Report Executions
        Route::get('report-executions-pending', [ReportExecutionController::class, 'pending'])->name('report-executions.pending');
        Route::get('report-executions-completed', [ReportExecutionController::class, 'completed'])->name('report-executions.completed');
        Route::get('report-executions-failed', [ReportExecutionController::class, 'failed'])->name('report-executions.failed');
        Route::post('report-executions/{report_execution}/mark-as-completed', [ReportExecutionController::class, 'markAsCompleted'])->name('report-executions.mark-as-completed');
        Route::post('report-executions/{report_execution}/mark-as-failed', [ReportExecutionController::class, 'markAsFailed'])->name('report-executions.mark-as-failed');
        Route::apiResource('report-executions', ReportExecutionController::class);

        // KPI Definitions
        Route::get('kpi-definitions-active', [KpiDefinitionController::class, 'active'])->name('kpi-definitions.active');
        Route::post('kpi-definitions/{kpi_definition}/activate', [KpiDefinitionController::class, 'activate'])->name('kpi-definitions.activate');
        Route::post('kpi-definitions/{kpi_definition}/deactivate', [KpiDefinitionController::class, 'deactivate'])->name('kpi-definitions.deactivate');
        Route::apiResource('kpi-definitions', KpiDefinitionController::class);

        // Webhooks
        Route::get('webhooks-active', [WebhookController::class, 'active'])->name('webhooks.active');
        Route::post('webhooks/{webhook}/activate', [WebhookController::class, 'activate'])->name('webhooks.activate');
        Route::post('webhooks/{webhook}/deactivate', [WebhookController::class, 'deactivate'])->name('webhooks.deactivate');
        Route::post('webhooks/{webhook}/test', [WebhookController::class, 'test'])->name('webhooks.test');
        Route::post('webhooks/{webhook}/trigger', [WebhookController::class, 'trigger'])->name('webhooks.trigger');
        Route::apiResource('webhooks', WebhookController::class);

        // Webhook Logs
        Route::get('webhook-logs-failed', [WebhookLogController::class, 'failed'])->name('webhook-logs.failed');
        Route::get('webhook-logs-pending-retries', [WebhookLogController::class, 'pendingRetries'])->name('webhook-logs.pending-retries');
        Route::post('webhook-logs/{webhook_log}/retry', [WebhookLogController::class, 'retry'])->name('webhook-logs.retry');
        Route::apiResource('webhook-logs', WebhookLogController::class)->only(['index', 'show']);

        // Integrations
        Route::get('integrations-active', [IntegrationController::class, 'active'])->name('integrations.active');
        Route::get('integrations-type/{type}', [IntegrationController::class, 'byType'])->name('integrations.by-type');
        Route::get('integrations-provider/{provider}', [IntegrationController::class, 'byProvider'])->name('integrations.by-provider');
        Route::post('integrations/{integration}/activate', [IntegrationController::class, 'activate'])->name('integrations.activate');
        Route::post('integrations/{integration}/deactivate', [IntegrationController::class, 'deactivate'])->name('integrations.deactivate');
        Route::post('integrations/{integration}/test-connection', [IntegrationController::class, 'testConnection'])->name('integrations.test-connection');
        Route::post('integrations/{integration}/sync', [IntegrationController::class, 'sync'])->name('integrations.sync');
        Route::apiResource('integrations', IntegrationController::class);
    });
});
