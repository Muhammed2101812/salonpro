<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\AppointmentCancellationRepositoryInterface;
use App\Repositories\Contracts\AppointmentHistoryRepositoryInterface;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\BankAccountRepositoryInterface;
use App\Repositories\Contracts\BranchRepositoryInterface;
use App\Repositories\Contracts\CashRegisterRepositoryInterface;
use App\Repositories\Contracts\CouponUsageRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\EmployeeAttendanceRepositoryInterface;
use App\Repositories\Contracts\EmployeeCommissionRepositoryInterface;
use App\Repositories\Contracts\EmployeeLeaveRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\EmployeeShiftRepositoryInterface;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\Contracts\IntegrationRepositoryInterface;
use App\Repositories\Contracts\InventoryMovementRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\LoyaltyPointRepositoryInterface;
use App\Repositories\Contracts\NotificationQueueRepositoryInterface;
use App\Repositories\Contracts\NotificationTemplateRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\ProductAttributeRepositoryInterface;
use App\Repositories\Contracts\ProductAttributeValueRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use App\Repositories\Contracts\PurchaseOrderRepositoryInterface;
use App\Repositories\Contracts\ReportExecutionRepositoryInterface;
use App\Repositories\Contracts\ReportTemplateRepositoryInterface;
use App\Repositories\Contracts\KpiDefinitionRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Repositories\Contracts\ServiceCategoryRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\ServiceReviewRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Repositories\Contracts\StockAlertRepositoryInterface;
use App\Repositories\Contracts\StockTransferRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Repositories\Contracts\TaxRateRepositoryInterface;
use App\Repositories\Contracts\WebhookLogRepositoryInterface;
use App\Repositories\Contracts\WebhookRepositoryInterface;
use App\Repositories\Eloquent\AppointmentCancellationRepository;
use App\Repositories\Eloquent\AppointmentHistoryRepository;
use App\Repositories\Eloquent\AppointmentRepository;
use App\Repositories\Eloquent\BankAccountRepository;
use App\Repositories\Eloquent\BranchRepository;
use App\Repositories\Eloquent\CashRegisterRepository;
use App\Repositories\Eloquent\CouponUsageRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\EmployeeAttendanceRepository;
use App\Repositories\Eloquent\EmployeeCommissionRepository;
use App\Repositories\Eloquent\EmployeeLeaveRepository;
use App\Repositories\Eloquent\EmployeeRepository;
use App\Repositories\Eloquent\EmployeeShiftRepository;
use App\Repositories\Eloquent\ExpenseRepository;
use App\Repositories\Eloquent\IntegrationRepository;
use App\Repositories\Eloquent\InventoryMovementRepository;
use App\Repositories\Eloquent\InvoiceRepository;
use App\Repositories\Eloquent\LoyaltyPointRepository;
use App\Repositories\Eloquent\NotificationQueueRepository;
use App\Repositories\Eloquent\NotificationTemplateRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\ProductAttributeRepository;
use App\Repositories\Eloquent\ProductAttributeValueRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\ProductVariantRepository;
use App\Repositories\Eloquent\PurchaseOrderRepository;
use App\Repositories\Eloquent\ReportExecutionRepository;
use App\Repositories\Eloquent\ReportTemplateRepository;
use App\Repositories\Eloquent\KpiDefinitionRepository;
use App\Repositories\Eloquent\SaleRepository;
use App\Repositories\Eloquent\ServiceCategoryRepository;
use App\Repositories\Eloquent\ServiceRepository;
use App\Repositories\Eloquent\ServiceReviewRepository;
use App\Repositories\Eloquent\SettingRepository;
use App\Repositories\Eloquent\StockAlertRepository;
use App\Repositories\Eloquent\StockTransferRepository;
use App\Repositories\Eloquent\SupplierRepository;
use App\Repositories\Eloquent\TaxRateRepository;
use App\Repositories\Eloquent\WebhookLogRepository;
use App\Repositories\Eloquent\WebhookRepository;
use App\Services\AppointmentHistoryService;
use App\Services\Contracts\AppointmentHistoryServiceInterface;
use App\Services\Contracts\BankAccountServiceInterface;
use App\Services\Contracts\CashRegisterServiceInterface;
use App\Services\Contracts\CouponServiceInterface;
use App\Services\Contracts\EmployeeAttendanceServiceInterface;
use App\Services\Contracts\EmployeeCommissionServiceInterface;
use App\Services\Contracts\EmployeeLeaveServiceInterface;
use App\Services\Contracts\IntegrationServiceInterface;
use App\Services\Contracts\InvoiceServiceInterface;
use App\Services\Contracts\LoyaltyPointServiceInterface;
use App\Services\Contracts\NotificationServiceInterface;
use App\Services\Contracts\ProductAttributeServiceInterface;
use App\Services\Contracts\ProductAttributeValueServiceInterface;
use App\Services\Contracts\ProductVariantServiceInterface;
use App\Services\Contracts\PurchaseOrderServiceInterface;
use App\Services\Contracts\ReportExecutionServiceInterface;
use App\Services\Contracts\ReportTemplateServiceInterface;
use App\Services\Contracts\KpiDefinitionServiceInterface;
use App\Services\Contracts\ServiceReviewServiceInterface;
use App\Services\Contracts\StockAlertServiceInterface;
use App\Services\Contracts\StockTransferServiceInterface;
use App\Services\Contracts\SupplierServiceInterface;
use App\Services\Contracts\TaxServiceInterface;
use App\Services\Contracts\WebhookLogServiceInterface;
use App\Services\Contracts\WebhookServiceInterface;
use App\Services\BankAccountService;
use App\Services\CashRegisterService;
use App\Services\CouponService;
use App\Services\EmployeeAttendanceService;
use App\Services\EmployeeCommissionService;
use App\Services\EmployeeLeaveService;
use App\Services\IntegrationService;
use App\Services\InvoiceService;
use App\Services\LoyaltyPointService;
use App\Services\NotificationService;
use App\Services\ProductAttributeService;
use App\Services\ProductAttributeValueService;
use App\Services\ProductVariantService;
use App\Services\PurchaseOrderService;
use App\Services\ReportExecutionService;
use App\Services\ReportTemplateService;
use App\Services\KpiDefinitionService;
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
        // Repository bindings
        AppointmentRepositoryInterface::class => AppointmentRepository::class,
        AppointmentCancellationRepositoryInterface::class => AppointmentCancellationRepository::class,
        AppointmentHistoryRepositoryInterface::class => AppointmentHistoryRepository::class,
        BankAccountRepositoryInterface::class => BankAccountRepository::class,
        BranchRepositoryInterface::class => BranchRepository::class,
        CashRegisterRepositoryInterface::class => CashRegisterRepository::class,
        CouponUsageRepositoryInterface::class => CouponUsageRepository::class,
        CustomerRepositoryInterface::class => CustomerRepository::class,
        EmployeeAttendanceRepositoryInterface::class => EmployeeAttendanceRepository::class,
        EmployeeCommissionRepositoryInterface::class => EmployeeCommissionRepository::class,
        EmployeeLeaveRepositoryInterface::class => EmployeeLeaveRepository::class,
        EmployeeRepositoryInterface::class => EmployeeRepository::class,
        EmployeeShiftRepositoryInterface::class => EmployeeShiftRepository::class,
        ExpenseRepositoryInterface::class => ExpenseRepository::class,
        IntegrationRepositoryInterface::class => IntegrationRepository::class,
        InventoryMovementRepositoryInterface::class => InventoryMovementRepository::class,
        InvoiceRepositoryInterface::class => InvoiceRepository::class,
        LoyaltyPointRepositoryInterface::class => LoyaltyPointRepository::class,
        NotificationQueueRepositoryInterface::class => NotificationQueueRepository::class,
        NotificationTemplateRepositoryInterface::class => NotificationTemplateRepository::class,
        PaymentRepositoryInterface::class => PaymentRepository::class,
        ProductAttributeRepositoryInterface::class => ProductAttributeRepository::class,
        ProductAttributeValueRepositoryInterface::class => ProductAttributeValueRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        ProductVariantRepositoryInterface::class => ProductVariantRepository::class,
        PurchaseOrderRepositoryInterface::class => PurchaseOrderRepository::class,
        ReportExecutionRepositoryInterface::class => ReportExecutionRepository::class,
        ReportTemplateRepositoryInterface::class => ReportTemplateRepository::class,
        KpiDefinitionRepositoryInterface::class => KpiDefinitionRepository::class,
        SaleRepositoryInterface::class => SaleRepository::class,
        ServiceRepositoryInterface::class => ServiceRepository::class,
        ServiceCategoryRepositoryInterface::class => ServiceCategoryRepository::class,
        ServiceReviewRepositoryInterface::class => ServiceReviewRepository::class,
        SettingRepositoryInterface::class => SettingRepository::class,
        StockAlertRepositoryInterface::class => StockAlertRepository::class,
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
        LoyaltyPointServiceInterface::class => LoyaltyPointService::class,
        NotificationServiceInterface::class => NotificationService::class,
        ProductAttributeServiceInterface::class => ProductAttributeService::class,
        ProductAttributeValueServiceInterface::class => ProductAttributeValueService::class,
        ProductVariantServiceInterface::class => ProductVariantService::class,
        PurchaseOrderServiceInterface::class => PurchaseOrderService::class,
        ReportExecutionServiceInterface::class => ReportExecutionService::class,
        ReportTemplateServiceInterface::class => ReportTemplateService::class,
        KpiDefinitionServiceInterface::class => KpiDefinitionService::class,
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
