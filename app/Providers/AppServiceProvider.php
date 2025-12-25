<?php

declare(strict_types=1);

namespace App\Providers;

// Repository Contracts
use App\Repositories\Contracts\AppointmentCancellationReasonRepositoryInterface;
use App\Repositories\Contracts\AppointmentCancellationRepositoryInterface;
use App\Repositories\Contracts\AppointmentConflictRepositoryInterface;
use App\Repositories\Contracts\AppointmentGroupParticipantRepositoryInterface;
use App\Repositories\Contracts\AppointmentGroupRepositoryInterface;
use App\Repositories\Contracts\AppointmentHistoryRepositoryInterface;
use App\Repositories\Contracts\AppointmentRecurrenceRepositoryInterface;
use App\Repositories\Contracts\AppointmentReminderRepositoryInterface;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\AppointmentWaitlistRepositoryInterface;
use App\Repositories\Contracts\BankAccountRepositoryInterface;
use App\Repositories\Contracts\BranchRepositoryInterface;
use App\Repositories\Contracts\BranchSettingRepositoryInterface;
use App\Repositories\Contracts\CampaignStatisticRepositoryInterface;
use App\Repositories\Contracts\CashRegisterRepositoryInterface;
use App\Repositories\Contracts\CouponUsageRepositoryInterface;
use App\Repositories\Contracts\CustomerCategoryRepositoryInterface;
use App\Repositories\Contracts\CustomerNoteRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\CustomerTagRepositoryInterface;
use App\Repositories\Contracts\EmailProviderRepositoryInterface;
use App\Repositories\Contracts\EmployeeAttendanceRepositoryInterface;
use App\Repositories\Contracts\EmployeeCertificationRepositoryInterface;
use App\Repositories\Contracts\EmployeeCommissionRepositoryInterface;
use App\Repositories\Contracts\EmployeeLeaveRepositoryInterface;
use App\Repositories\Contracts\EmployeePerformanceRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\EmployeeScheduleRepositoryInterface;
use App\Repositories\Contracts\EmployeeShiftRepositoryInterface;
use App\Repositories\Contracts\EmployeeSkillRepositoryInterface;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\Contracts\IntegrationRepositoryInterface;
use App\Repositories\Contracts\InventoryMovementRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\KpiDefinitionRepositoryInterface;
use App\Repositories\Contracts\LoyaltyPointRepositoryInterface;
use App\Repositories\Contracts\NotificationCampaignRepositoryInterface;
use App\Repositories\Contracts\NotificationLogRepositoryInterface;
use App\Repositories\Contracts\NotificationPreferenceRepositoryInterface;
use App\Repositories\Contracts\NotificationQueueRepositoryInterface;
use App\Repositories\Contracts\NotificationTemplateRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\ProductAttributeRepositoryInterface;
use App\Repositories\Contracts\ProductAttributeValueRepositoryInterface;
use App\Repositories\Contracts\ProductBundleRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use App\Repositories\Contracts\PurchaseOrderItemRepositoryInterface;
use App\Repositories\Contracts\PurchaseOrderRepositoryInterface;
use App\Repositories\Contracts\ReportExecutionRepositoryInterface;
use App\Repositories\Contracts\ReportTemplateRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Repositories\Contracts\ServiceAddonRepositoryInterface;
use App\Repositories\Contracts\ServiceCategoryRepositoryInterface;
use App\Repositories\Contracts\ServicePackageRepositoryInterface;
use App\Repositories\Contracts\ServicePriceHistoryRepositoryInterface;
use App\Repositories\Contracts\ServicePricingRuleRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\ServiceRequirementRepositoryInterface;
use App\Repositories\Contracts\ServiceReviewRepositoryInterface;
use App\Repositories\Contracts\ServiceTemplateRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Repositories\Contracts\SmsProviderRepositoryInterface;
use App\Repositories\Contracts\StockAlertRepositoryInterface;
use App\Repositories\Contracts\StockAuditRepositoryInterface;
use App\Repositories\Contracts\StockTransferRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Repositories\Contracts\TaxRateRepositoryInterface;
use App\Repositories\Contracts\WebhookLogRepositoryInterface;
use App\Repositories\Contracts\WebhookRepositoryInterface;

// Repository Eloquent Implementations
use App\Repositories\Eloquent\AppointmentCancellationReasonRepository;
use App\Repositories\Eloquent\AppointmentCancellationRepository;
use App\Repositories\Eloquent\AppointmentConflictRepository;
use App\Repositories\Eloquent\AppointmentGroupParticipantRepository;
use App\Repositories\Eloquent\AppointmentGroupRepository;
use App\Repositories\Eloquent\AppointmentHistoryRepository;
use App\Repositories\Eloquent\AppointmentRecurrenceRepository;
use App\Repositories\Eloquent\AppointmentReminderRepository;
use App\Repositories\Eloquent\AppointmentRepository;
use App\Repositories\Eloquent\AppointmentWaitlistRepository;
use App\Repositories\Eloquent\BankAccountRepository;
use App\Repositories\Eloquent\BranchRepository;
use App\Repositories\Eloquent\BranchSettingRepository;
use App\Repositories\Eloquent\CampaignStatisticRepository;
use App\Repositories\Eloquent\CashRegisterRepository;
use App\Repositories\Eloquent\CouponUsageRepository;
use App\Repositories\Eloquent\CustomerCategoryRepository;
use App\Repositories\Eloquent\CustomerNoteRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\CustomerTagRepository;
use App\Repositories\Eloquent\EmailProviderRepository;
use App\Repositories\Eloquent\EmployeeAttendanceRepository;
use App\Repositories\Eloquent\EmployeeCertificationRepository;
use App\Repositories\Eloquent\EmployeeCommissionRepository;
use App\Repositories\Eloquent\EmployeeLeaveRepository;
use App\Repositories\Eloquent\EmployeePerformanceRepository;
use App\Repositories\Eloquent\EmployeeRepository;
use App\Repositories\Eloquent\EmployeeScheduleRepository;
use App\Repositories\Eloquent\EmployeeShiftRepository;
use App\Repositories\Eloquent\EmployeeSkillRepository;
use App\Repositories\Eloquent\ExpenseRepository;
use App\Repositories\Eloquent\IntegrationRepository;
use App\Repositories\Eloquent\InventoryMovementRepository;
use App\Repositories\Eloquent\InvoiceRepository;
use App\Repositories\Eloquent\KpiDefinitionRepository;
use App\Repositories\Eloquent\LoyaltyPointRepository;
use App\Repositories\Eloquent\NotificationCampaignRepository;
use App\Repositories\Eloquent\NotificationLogRepository;
use App\Repositories\Eloquent\NotificationPreferenceRepository;
use App\Repositories\Eloquent\NotificationQueueRepository;
use App\Repositories\Eloquent\NotificationTemplateRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\ProductAttributeRepository;
use App\Repositories\Eloquent\ProductAttributeValueRepository;
use App\Repositories\Eloquent\ProductBundleRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\ProductVariantRepository;
use App\Repositories\Eloquent\PurchaseOrderItemRepository;
use App\Repositories\Eloquent\PurchaseOrderRepository;
use App\Repositories\Eloquent\ReportExecutionRepository;
use App\Repositories\Eloquent\ReportTemplateRepository;
use App\Repositories\Eloquent\SaleRepository;
use App\Repositories\Eloquent\ServiceAddonRepository;
use App\Repositories\Eloquent\ServiceCategoryRepository;
use App\Repositories\Eloquent\ServicePackageRepository;
use App\Repositories\Eloquent\ServicePriceHistoryRepository;
use App\Repositories\Eloquent\ServicePricingRuleRepository;
use App\Repositories\Eloquent\ServiceRepository;
use App\Repositories\Eloquent\ServiceRequirementRepository;
use App\Repositories\Eloquent\ServiceReviewRepository;
use App\Repositories\Eloquent\ServiceTemplateRepository;
use App\Repositories\Eloquent\SettingRepository;
use App\Repositories\Eloquent\SmsProviderRepository;
use App\Repositories\Eloquent\StockAlertRepository;
use App\Repositories\Eloquent\StockAuditRepository;
use App\Repositories\Eloquent\StockTransferRepository;
use App\Repositories\Eloquent\SupplierRepository;
use App\Repositories\Eloquent\TaxRateRepository;
use App\Repositories\Eloquent\WebhookLogRepository;
use App\Repositories\Eloquent\WebhookRepository;

// Service Contracts
use App\Services\Contracts\AppointmentHistoryServiceInterface;
use App\Services\Contracts\BankAccountServiceInterface;
use App\Services\Contracts\CashRegisterServiceInterface;
use App\Services\Contracts\CouponServiceInterface;
use App\Services\Contracts\EmployeeAttendanceServiceInterface;
use App\Services\Contracts\EmployeeCommissionServiceInterface;
use App\Services\Contracts\EmployeeLeaveServiceInterface;
use App\Services\Contracts\IntegrationServiceInterface;
use App\Services\Contracts\InvoiceServiceInterface;
use App\Services\Contracts\KpiDefinitionServiceInterface;
use App\Services\Contracts\LoyaltyPointServiceInterface;
use App\Services\Contracts\NotificationServiceInterface;
use App\Services\Contracts\ProductAttributeServiceInterface;
use App\Services\Contracts\ProductAttributeValueServiceInterface;
use App\Services\Contracts\ProductVariantServiceInterface;
use App\Services\Contracts\PurchaseOrderServiceInterface;
use App\Services\Contracts\ReportExecutionServiceInterface;
use App\Services\Contracts\ReportTemplateServiceInterface;
use App\Services\Contracts\ServiceReviewServiceInterface;
use App\Services\Contracts\StockAlertServiceInterface;
use App\Services\Contracts\StockTransferServiceInterface;
use App\Services\Contracts\SupplierServiceInterface;
use App\Services\Contracts\TaxServiceInterface;
use App\Services\Contracts\WebhookLogServiceInterface;
use App\Services\Contracts\WebhookServiceInterface;

// Service Implementations
use App\Services\AppointmentHistoryService;
use App\Services\BankAccountService;
use App\Services\CashRegisterService;
use App\Services\CouponService;
use App\Services\EmployeeAttendanceService;
use App\Services\EmployeeCommissionService;
use App\Services\EmployeeLeaveService;
use App\Services\IntegrationService;
use App\Services\InvoiceService;
use App\Services\KpiDefinitionService;
use App\Services\LoyaltyPointService;
use App\Services\NotificationService;
use App\Services\ProductAttributeService;
use App\Services\ProductAttributeValueService;
use App\Services\ProductVariantService;
use App\Services\PurchaseOrderService;
use App\Services\ReportExecutionService;
use App\Services\ReportTemplateService;
use App\Services\ServiceReviewService;
use App\Services\StockAlertService;
use App\Services\StockTransferService;
use App\Services\SupplierService;
use App\Services\TaxService;
use App\Services\WebhookLogService;
use App\Services\WebhookService;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array<string, string>
     */
    public array $bindings = [
        // Repository bindings - All 70 repositories
        AppointmentCancellationReasonRepositoryInterface::class => AppointmentCancellationReasonRepository::class,
        AppointmentCancellationRepositoryInterface::class => AppointmentCancellationRepository::class,
        AppointmentConflictRepositoryInterface::class => AppointmentConflictRepository::class,
        AppointmentGroupParticipantRepositoryInterface::class => AppointmentGroupParticipantRepository::class,
        AppointmentGroupRepositoryInterface::class => AppointmentGroupRepository::class,
        AppointmentHistoryRepositoryInterface::class => AppointmentHistoryRepository::class,
        AppointmentRecurrenceRepositoryInterface::class => AppointmentRecurrenceRepository::class,
        AppointmentReminderRepositoryInterface::class => AppointmentReminderRepository::class,
        AppointmentRepositoryInterface::class => AppointmentRepository::class,
        AppointmentWaitlistRepositoryInterface::class => AppointmentWaitlistRepository::class,
        BankAccountRepositoryInterface::class => BankAccountRepository::class,
        BranchRepositoryInterface::class => BranchRepository::class,
        BranchSettingRepositoryInterface::class => BranchSettingRepository::class,
        CampaignStatisticRepositoryInterface::class => CampaignStatisticRepository::class,
        CashRegisterRepositoryInterface::class => CashRegisterRepository::class,
        CouponUsageRepositoryInterface::class => CouponUsageRepository::class,
        CustomerCategoryRepositoryInterface::class => CustomerCategoryRepository::class,
        CustomerNoteRepositoryInterface::class => CustomerNoteRepository::class,
        CustomerRepositoryInterface::class => CustomerRepository::class,
        CustomerTagRepositoryInterface::class => CustomerTagRepository::class,
        EmailProviderRepositoryInterface::class => EmailProviderRepository::class,
        EmployeeAttendanceRepositoryInterface::class => EmployeeAttendanceRepository::class,
        EmployeeCertificationRepositoryInterface::class => EmployeeCertificationRepository::class,
        EmployeeCommissionRepositoryInterface::class => EmployeeCommissionRepository::class,
        EmployeeLeaveRepositoryInterface::class => EmployeeLeaveRepository::class,
        EmployeePerformanceRepositoryInterface::class => EmployeePerformanceRepository::class,
        EmployeeRepositoryInterface::class => EmployeeRepository::class,
        EmployeeScheduleRepositoryInterface::class => EmployeeScheduleRepository::class,
        EmployeeShiftRepositoryInterface::class => EmployeeShiftRepository::class,
        EmployeeSkillRepositoryInterface::class => EmployeeSkillRepository::class,
        ExpenseRepositoryInterface::class => ExpenseRepository::class,
        IntegrationRepositoryInterface::class => IntegrationRepository::class,
        InventoryMovementRepositoryInterface::class => InventoryMovementRepository::class,
        InvoiceRepositoryInterface::class => InvoiceRepository::class,
        KpiDefinitionRepositoryInterface::class => KpiDefinitionRepository::class,
        LoyaltyPointRepositoryInterface::class => LoyaltyPointRepository::class,
        NotificationCampaignRepositoryInterface::class => NotificationCampaignRepository::class,
        NotificationLogRepositoryInterface::class => NotificationLogRepository::class,
        NotificationPreferenceRepositoryInterface::class => NotificationPreferenceRepository::class,
        NotificationQueueRepositoryInterface::class => NotificationQueueRepository::class,
        NotificationTemplateRepositoryInterface::class => NotificationTemplateRepository::class,
        PaymentRepositoryInterface::class => PaymentRepository::class,
        ProductAttributeRepositoryInterface::class => ProductAttributeRepository::class,
        ProductAttributeValueRepositoryInterface::class => ProductAttributeValueRepository::class,
        ProductBundleRepositoryInterface::class => ProductBundleRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        ProductVariantRepositoryInterface::class => ProductVariantRepository::class,
        PurchaseOrderItemRepositoryInterface::class => PurchaseOrderItemRepository::class,
        PurchaseOrderRepositoryInterface::class => PurchaseOrderRepository::class,
        ReportExecutionRepositoryInterface::class => ReportExecutionRepository::class,
        ReportTemplateRepositoryInterface::class => ReportTemplateRepository::class,
        SaleRepositoryInterface::class => SaleRepository::class,
        ServiceAddonRepositoryInterface::class => ServiceAddonRepository::class,
        ServiceCategoryRepositoryInterface::class => ServiceCategoryRepository::class,
        ServicePackageRepositoryInterface::class => ServicePackageRepository::class,
        ServicePriceHistoryRepositoryInterface::class => ServicePriceHistoryRepository::class,
        ServicePricingRuleRepositoryInterface::class => ServicePricingRuleRepository::class,
        ServiceRepositoryInterface::class => ServiceRepository::class,
        ServiceRequirementRepositoryInterface::class => ServiceRequirementRepository::class,
        ServiceReviewRepositoryInterface::class => ServiceReviewRepository::class,
        ServiceTemplateRepositoryInterface::class => ServiceTemplateRepository::class,
        SettingRepositoryInterface::class => SettingRepository::class,
        SmsProviderRepositoryInterface::class => SmsProviderRepository::class,
        StockAlertRepositoryInterface::class => StockAlertRepository::class,
        StockAuditRepositoryInterface::class => StockAuditRepository::class,
        StockTransferRepositoryInterface::class => StockTransferRepository::class,
        SupplierRepositoryInterface::class => SupplierRepository::class,
        TaxRateRepositoryInterface::class => TaxRateRepository::class,
        WebhookLogRepositoryInterface::class => WebhookLogRepository::class,
        WebhookRepositoryInterface::class => WebhookRepository::class,

        // Service bindings
        AppointmentHistoryServiceInterface::class => AppointmentHistoryService::class,
        BankAccountServiceInterface::class => BankAccountService::class,
        CashRegisterServiceInterface::class => CashRegisterService::class,
        CouponServiceInterface::class => CouponService::class,
        EmployeeAttendanceServiceInterface::class => EmployeeAttendanceService::class,
        EmployeeCommissionServiceInterface::class => EmployeeCommissionService::class,
        EmployeeLeaveServiceInterface::class => EmployeeLeaveService::class,
        IntegrationServiceInterface::class => IntegrationService::class,
        InvoiceServiceInterface::class => InvoiceService::class,
        KpiDefinitionServiceInterface::class => KpiDefinitionService::class,
        LoyaltyPointServiceInterface::class => LoyaltyPointService::class,
        NotificationServiceInterface::class => NotificationService::class,
        ProductAttributeServiceInterface::class => ProductAttributeService::class,
        ProductAttributeValueServiceInterface::class => ProductAttributeValueService::class,
        ProductVariantServiceInterface::class => ProductVariantService::class,
        PurchaseOrderServiceInterface::class => PurchaseOrderService::class,
        ReportExecutionServiceInterface::class => ReportExecutionService::class,
        ReportTemplateServiceInterface::class => ReportTemplateService::class,
        ServiceReviewServiceInterface::class => ServiceReviewService::class,
        StockAlertServiceInterface::class => StockAlertService::class,
        StockTransferServiceInterface::class => StockTransferService::class,
        SupplierServiceInterface::class => SupplierService::class,
        TaxServiceInterface::class => TaxService::class,
        WebhookLogServiceInterface::class => WebhookLogService::class,
        WebhookServiceInterface::class => WebhookService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register event listeners
        Event::listen(
            \App\Events\Appointment\AppointmentCreated::class,
            [\App\Listeners\Appointment\SendAppointmentConfirmation::class]
        );

        // Register model observers
        \App\Models\Customer::observe(\App\Observers\CustomerObserver::class);
    }
}
