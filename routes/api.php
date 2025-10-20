<?php

declare(strict_types=1);

use App\Http\Controllers\API\ActivityLogController;
use App\Http\Controllers\API\AnalyticsEventController;
use App\Http\Controllers\API\AnalyticsSessionController;
use App\Http\Controllers\API\AppointmentCancellationController;
use App\Http\Controllers\API\AppointmentCancellationReasonController;
use App\Http\Controllers\API\AppointmentConflictController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\AppointmentGroupController;
use App\Http\Controllers\API\AppointmentGroupParticipantController;
use App\Http\Controllers\API\AppointmentHistoryController;
use App\Http\Controllers\API\AppointmentRecurrenceController;
use App\Http\Controllers\API\AppointmentReminderController;
use App\Http\Controllers\API\AppointmentWaitlistController;
use App\Http\Controllers\API\AuditLogController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BankAccountController;
use App\Http\Controllers\API\BankTransactionController;
use App\Http\Controllers\API\BranchController;
use App\Http\Controllers\API\BranchSettingController;
use App\Http\Controllers\API\BudgetItemController;
use App\Http\Controllers\API\BudgetPlanController;
use App\Http\Controllers\API\CampaignStatisticController;
use App\Http\Controllers\API\CashRegisterController;
use App\Http\Controllers\API\CashRegisterSessionController;
use App\Http\Controllers\API\CashRegisterTransactionController;
use App\Http\Controllers\API\ChartOfAccountController;
use App\Http\Controllers\API\CouponController;
use App\Http\Controllers\API\CouponUsageController;
use App\Http\Controllers\API\CurrencyController;
use App\Http\Controllers\API\CustomFieldController;
use App\Http\Controllers\API\CustomFieldValueController;
use App\Http\Controllers\API\CustomerCategoryController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\CustomerFeedbackController;
use App\Http\Controllers\API\CustomerNoteController;
use App\Http\Controllers\API\CustomerRfmAnalysisController;
use App\Http\Controllers\API\CustomerSegmentController;
use App\Http\Controllers\API\CustomerSegmentMemberController;
use App\Http\Controllers\API\CustomerTagController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\DashboardWidgetController;
use App\Http\Controllers\API\DocumentTemplateController;
use App\Http\Controllers\API\EmailProviderController;
use App\Http\Controllers\API\EmployeeAttendanceController;
use App\Http\Controllers\API\EmployeeCertificationController;
use App\Http\Controllers\API\EmployeeCommissionController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\EmployeeLeaveController;
use App\Http\Controllers\API\EmployeePerformanceController;
use App\Http\Controllers\API\EmployeeScheduleController;
use App\Http\Controllers\API\EmployeeShiftController;
use App\Http\Controllers\API\EmployeeSkillController;
use App\Http\Controllers\API\ExchangeRateController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\FeatureFlagController;
use App\Http\Controllers\API\IntegrationController;
use App\Http\Controllers\API\InventoryMovementController;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\InvoiceItemController;
use App\Http\Controllers\API\JournalEntryController;
use App\Http\Controllers\API\JournalEntryLineController;
use App\Http\Controllers\API\KpiDefinitionController;
use App\Http\Controllers\API\KpiValueController;
use App\Http\Controllers\API\LeadActivityController;
use App\Http\Controllers\API\LeadController;
use App\Http\Controllers\API\LoyaltyPointController;
use App\Http\Controllers\API\LoyaltyPointTransactionController;
use App\Http\Controllers\API\LoyaltyProgramController;
use App\Http\Controllers\API\MarketingCampaignController;
use App\Http\Controllers\API\NotificationCampaignController;
use App\Http\Controllers\API\NotificationLogController;
use App\Http\Controllers\API\NotificationPreferenceController;
use App\Http\Controllers\API\NotificationQueueController;
use App\Http\Controllers\API\NotificationTemplateController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PerformanceMetricController;
use App\Http\Controllers\API\ProductAttributeController;
use App\Http\Controllers\API\ProductAttributeValueController;
use App\Http\Controllers\API\ProductBarcodeController;
use App\Http\Controllers\API\ProductBundleController;
use App\Http\Controllers\API\ProductBundleItemController;
use App\Http\Controllers\API\ProductCategoryHierarchyController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProductDiscountController;
use App\Http\Controllers\API\ProductImageController;
use App\Http\Controllers\API\ProductPriceHistoryController;
use App\Http\Controllers\API\ProductStockHistoryController;
use App\Http\Controllers\API\ProductSupplierPriceController;
use App\Http\Controllers\API\ProductVariantController;
use App\Http\Controllers\API\PurchaseOrderController;
use App\Http\Controllers\API\PurchaseOrderItemController;
use App\Http\Controllers\API\PushNotificationTokenController;
use App\Http\Controllers\API\ReferralController;
use App\Http\Controllers\API\ReferralProgramController;
use App\Http\Controllers\API\ReportExecutionController;
use App\Http\Controllers\API\ReportScheduleController;
use App\Http\Controllers\API\ReportTemplateController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\ServiceAddonController;
use App\Http\Controllers\API\ServiceCategoryController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\ServicePackageController;
use App\Http\Controllers\API\ServicePriceHistoryController;
use App\Http\Controllers\API\ServicePricingRuleController;
use App\Http\Controllers\API\ServiceRequirementController;
use App\Http\Controllers\API\ServiceReviewController;
use App\Http\Controllers\API\ServiceTemplateController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\SmsProviderController;
use App\Http\Controllers\API\StockAlertController;
use App\Http\Controllers\API\StockAuditController;
use App\Http\Controllers\API\StockAuditItemController;
use App\Http\Controllers\API\StockTransferController;
use App\Http\Controllers\API\SupplierController;
use App\Http\Controllers\API\SurveyController;
use App\Http\Controllers\API\SurveyResponseController;
use App\Http\Controllers\API\SystemBackupController;
use App\Http\Controllers\API\SystemSettingController;
use App\Http\Controllers\API\TaxRateController;
use App\Http\Controllers\API\TranslationController;
use App\Http\Controllers\API\UserPreferenceController;
use App\Http\Controllers\API\WebhookController;
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
        Route::apiResource('dashboard-widgets', DashboardWidgetController::class);

        // ===== BRANCHES & SETTINGS =====
        Route::apiResource('branches', BranchController::class);
        Route::apiResource('branch-settings', BranchSettingController::class);
        Route::apiResource('settings', SettingController::class)->only(['index', 'store', 'update']);
        Route::apiResource('system-settings', SystemSettingController::class);

        // ===== CUSTOMERS =====
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('customer-categories', CustomerCategoryController::class);
        Route::apiResource('customer-tags', CustomerTagController::class);
        Route::apiResource('customer-notes', CustomerNoteController::class);
        Route::apiResource('customer-feedback', CustomerFeedbackController::class);
        Route::apiResource('customer-segments', CustomerSegmentController::class);
        Route::apiResource('customer-segment-members', CustomerSegmentMemberController::class);
        Route::apiResource('customer-rfm-analysis', CustomerRfmAnalysisController::class);

        // ===== EMPLOYEES =====
        Route::apiResource('employees', EmployeeController::class);
        Route::apiResource('employee-skills', EmployeeSkillController::class);
        Route::apiResource('employee-certifications', EmployeeCertificationController::class);
        Route::apiResource('employee-schedules', EmployeeScheduleController::class);
        Route::apiResource('employee-shifts', EmployeeShiftController::class);
        Route::apiResource('employee-attendance', EmployeeAttendanceController::class);
        Route::apiResource('employee-performance', EmployeePerformanceController::class);
        Route::apiResource('employee-commissions', EmployeeCommissionController::class);
        Route::apiResource('employee-leave', EmployeeLeaveController::class);

        // ===== APPOINTMENTS =====
        Route::apiResource('appointments', AppointmentController::class);
        Route::apiResource('appointment-cancellations', AppointmentCancellationController::class);
        Route::apiResource('appointment-cancellation-reasons', AppointmentCancellationReasonController::class);
        Route::apiResource('appointment-conflicts', AppointmentConflictController::class);
        Route::apiResource('appointment-groups', AppointmentGroupController::class);
        Route::apiResource('appointment-group-participants', AppointmentGroupParticipantController::class);
        Route::apiResource('appointment-history', AppointmentHistoryController::class);
        Route::apiResource('appointment-recurrences', AppointmentRecurrenceController::class);
        Route::apiResource('appointment-reminders', AppointmentReminderController::class);
        Route::apiResource('appointment-waitlist', AppointmentWaitlistController::class);

        // ===== SERVICES =====
        Route::apiResource('service-categories', ServiceCategoryController::class);
        Route::apiResource('services', ServiceController::class);
        Route::apiResource('service-addons', ServiceAddonController::class);
        Route::apiResource('service-packages', ServicePackageController::class);
        Route::apiResource('service-pricing-rules', ServicePricingRuleController::class);
        Route::apiResource('service-price-history', ServicePriceHistoryController::class);
        Route::apiResource('service-templates', ServiceTemplateController::class);
        Route::apiResource('service-requirements', ServiceRequirementController::class);
        Route::apiResource('service-reviews', ServiceReviewController::class);

        // ===== PRODUCTS =====
        Route::apiResource('products', ProductController::class);
        Route::apiResource('product-bundles', ProductBundleController::class);
        Route::apiResource('product-bundle-items', ProductBundleItemController::class);
        Route::apiResource('product-variants', ProductVariantController::class);
        Route::apiResource('product-attributes', ProductAttributeController::class);
        Route::apiResource('product-attribute-values', ProductAttributeValueController::class);
        Route::apiResource('product-barcodes', ProductBarcodeController::class);
        Route::apiResource('product-discounts', ProductDiscountController::class);
        Route::apiResource('product-images', ProductImageController::class);
        Route::apiResource('product-price-history', ProductPriceHistoryController::class);
        Route::apiResource('product-stock-history', ProductStockHistoryController::class);
        Route::apiResource('product-supplier-prices', ProductSupplierPriceController::class);
        Route::apiResource('product-category-hierarchy', ProductCategoryHierarchyController::class);

        // ===== INVENTORY & SUPPLY CHAIN =====
        Route::apiResource('inventory-movements', InventoryMovementController::class);
        Route::apiResource('suppliers', SupplierController::class);
        Route::apiResource('purchase-orders', PurchaseOrderController::class);
        Route::apiResource('purchase-order-items', PurchaseOrderItemController::class);
        Route::apiResource('stock-alerts', StockAlertController::class);
        Route::apiResource('stock-audits', StockAuditController::class);
        Route::apiResource('stock-audit-items', StockAuditItemController::class);
        Route::apiResource('stock-transfers', StockTransferController::class);

        // ===== FINANCIAL MANAGEMENT =====
        Route::apiResource('invoices', InvoiceController::class);
        Route::apiResource('invoice-items', InvoiceItemController::class);
        Route::apiResource('payments', PaymentController::class);
        Route::apiResource('expenses', ExpenseController::class);
        Route::apiResource('bank-accounts', BankAccountController::class);
        Route::apiResource('bank-transactions', BankTransactionController::class);
        Route::apiResource('budget-plans', BudgetPlanController::class);
        Route::apiResource('budget-items', BudgetItemController::class);
        Route::apiResource('cash-registers', CashRegisterController::class);
        Route::apiResource('cash-register-sessions', CashRegisterSessionController::class);
        Route::apiResource('cash-register-transactions', CashRegisterTransactionController::class);
        Route::apiResource('chart-of-accounts', ChartOfAccountController::class);
        Route::apiResource('journal-entries', JournalEntryController::class);
        Route::apiResource('journal-entry-lines', JournalEntryLineController::class);
        Route::apiResource('tax-rates', TaxRateController::class);
        Route::apiResource('currencies', CurrencyController::class);
        Route::apiResource('exchange-rates', ExchangeRateController::class);

        // ===== SALES =====
        Route::apiResource('sales', SaleController::class);

        // ===== MARKETING & CRM =====
        Route::apiResource('marketing-campaigns', MarketingCampaignController::class);
        Route::apiResource('campaign-statistics', CampaignStatisticController::class);
        Route::apiResource('coupons', CouponController::class);
        Route::apiResource('coupon-usage', CouponUsageController::class);
        Route::apiResource('loyalty-programs', LoyaltyProgramController::class);
        Route::apiResource('loyalty-points', LoyaltyPointController::class);
        Route::apiResource('loyalty-point-transactions', LoyaltyPointTransactionController::class);
        Route::apiResource('referrals', ReferralController::class);
        Route::apiResource('referral-programs', ReferralProgramController::class);
        Route::apiResource('leads', LeadController::class);
        Route::apiResource('lead-activities', LeadActivityController::class);

        // ===== NOTIFICATIONS & COMMUNICATIONS =====
        Route::apiResource('notification-campaigns', NotificationCampaignController::class);
        Route::apiResource('notification-templates', NotificationTemplateController::class);
        Route::apiResource('notification-queue', NotificationQueueController::class);
        Route::apiResource('notification-logs', NotificationLogController::class);
        Route::apiResource('notification-preferences', NotificationPreferenceController::class);
        Route::apiResource('sms-providers', SmsProviderController::class);
        Route::apiResource('email-providers', EmailProviderController::class);
        Route::apiResource('push-notification-tokens', PushNotificationTokenController::class);

        // ===== ANALYTICS & REPORTING =====
        Route::apiResource('analytics-events', AnalyticsEventController::class);
        Route::apiResource('analytics-sessions', AnalyticsSessionController::class);
        Route::apiResource('kpi-definitions', KpiDefinitionController::class);
        Route::apiResource('kpi-values', KpiValueController::class);
        Route::apiResource('performance-metrics', PerformanceMetricController::class);
        Route::apiResource('report-templates', ReportTemplateController::class);
        Route::apiResource('report-schedules', ReportScheduleController::class);
        Route::apiResource('report-executions', ReportExecutionController::class);

        // ===== SYSTEM & UTILITIES =====
        Route::apiResource('activity-logs', ActivityLogController::class)->only(['index', 'show']);
        Route::apiResource('audit-logs', AuditLogController::class)->only(['index', 'show']);
        Route::apiResource('system-backups', SystemBackupController::class);
        Route::apiResource('webhooks', WebhookController::class);
        Route::apiResource('integrations', IntegrationController::class);
        Route::apiResource('custom-fields', CustomFieldController::class);
        Route::apiResource('custom-field-values', CustomFieldValueController::class);
        Route::apiResource('translations', TranslationController::class);
        Route::apiResource('feature-flags', FeatureFlagController::class);
        Route::apiResource('user-preferences', UserPreferenceController::class);
        Route::apiResource('document-templates', DocumentTemplateController::class);
        Route::apiResource('surveys', SurveyController::class);
        Route::apiResource('survey-responses', SurveyResponseController::class);
    });
});
