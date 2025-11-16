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
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\EmployeeShiftRepositoryInterface;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\Contracts\InventoryMovementRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\LoyaltyPointRepositoryInterface;
use App\Repositories\Contracts\NotificationQueueRepositoryInterface;
use App\Repositories\Contracts\NotificationTemplateRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use App\Repositories\Contracts\PurchaseOrderRepositoryInterface;
use App\Repositories\Contracts\ReportTemplateRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Repositories\Contracts\ServiceCategoryRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\ServiceReviewRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Repositories\Contracts\StockTransferRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Repositories\Contracts\TaxRateRepositoryInterface;
use App\Repositories\Eloquent\AppointmentCancellationRepository;
use App\Repositories\Eloquent\AppointmentHistoryRepository;
use App\Repositories\Eloquent\AppointmentRepository;
use App\Repositories\Eloquent\BankAccountRepository;
use App\Repositories\Eloquent\BranchRepository;
use App\Repositories\Eloquent\CashRegisterRepository;
use App\Repositories\Eloquent\CouponUsageRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\EmployeeRepository;
use App\Repositories\Eloquent\EmployeeShiftRepository;
use App\Repositories\Eloquent\ExpenseRepository;
use App\Repositories\Eloquent\InventoryMovementRepository;
use App\Repositories\Eloquent\InvoiceRepository;
use App\Repositories\Eloquent\LoyaltyPointRepository;
use App\Repositories\Eloquent\NotificationQueueRepository;
use App\Repositories\Eloquent\NotificationTemplateRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\ProductVariantRepository;
use App\Repositories\Eloquent\PurchaseOrderRepository;
use App\Repositories\Eloquent\ReportTemplateRepository;
use App\Repositories\Eloquent\SaleRepository;
use App\Repositories\Eloquent\ServiceCategoryRepository;
use App\Repositories\Eloquent\ServiceRepository;
use App\Repositories\Eloquent\ServiceReviewRepository;
use App\Repositories\Eloquent\SettingRepository;
use App\Repositories\Eloquent\StockTransferRepository;
use App\Repositories\Eloquent\SupplierRepository;
use App\Repositories\Eloquent\TaxRateRepository;
use App\Services\AppointmentHistoryService;
use App\Services\Contracts\AppointmentHistoryServiceInterface;
use App\Services\Contracts\BankAccountServiceInterface;
use App\Services\Contracts\CashRegisterServiceInterface;
use App\Services\Contracts\CouponServiceInterface;
use App\Services\Contracts\InvoiceServiceInterface;
use App\Services\Contracts\LoyaltyPointServiceInterface;
use App\Services\Contracts\NotificationServiceInterface;
use App\Services\Contracts\ProductVariantServiceInterface;
use App\Services\Contracts\PurchaseOrderServiceInterface;
use App\Services\Contracts\ServiceReviewServiceInterface;
use App\Services\Contracts\StockTransferServiceInterface;
use App\Services\Contracts\SupplierServiceInterface;
use App\Services\Contracts\TaxServiceInterface;
use App\Services\BankAccountService;
use App\Services\CashRegisterService;
use App\Services\CouponService;
use App\Services\InvoiceService;
use App\Services\LoyaltyPointService;
use App\Services\NotificationService;
use App\Services\ProductVariantService;
use App\Services\PurchaseOrderService;
use App\Services\ServiceReviewService;
use App\Services\StockTransferService;
use App\Services\SupplierService;
use App\Services\TaxService;
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
        EmployeeRepositoryInterface::class => EmployeeRepository::class,
        EmployeeShiftRepositoryInterface::class => EmployeeShiftRepository::class,
        ExpenseRepositoryInterface::class => ExpenseRepository::class,
        InventoryMovementRepositoryInterface::class => InventoryMovementRepository::class,
        InvoiceRepositoryInterface::class => InvoiceRepository::class,
        LoyaltyPointRepositoryInterface::class => LoyaltyPointRepository::class,
        NotificationQueueRepositoryInterface::class => NotificationQueueRepository::class,
        NotificationTemplateRepositoryInterface::class => NotificationTemplateRepository::class,
        PaymentRepositoryInterface::class => PaymentRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        ProductVariantRepositoryInterface::class => ProductVariantRepository::class,
        PurchaseOrderRepositoryInterface::class => PurchaseOrderRepository::class,
        ReportTemplateRepositoryInterface::class => ReportTemplateRepository::class,
        SaleRepositoryInterface::class => SaleRepository::class,
        ServiceRepositoryInterface::class => ServiceRepository::class,
        ServiceCategoryRepositoryInterface::class => ServiceCategoryRepository::class,
        ServiceReviewRepositoryInterface::class => ServiceReviewRepository::class,
        SettingRepositoryInterface::class => SettingRepository::class,
        StockTransferRepositoryInterface::class => StockTransferRepository::class,
        SupplierRepositoryInterface::class => SupplierRepository::class,
        TaxRateRepositoryInterface::class => TaxRateRepository::class,

        // Service bindings
        AppointmentHistoryServiceInterface::class => AppointmentHistoryService::class,
        BankAccountServiceInterface::class => BankAccountService::class,
        CashRegisterServiceInterface::class => CashRegisterService::class,
        CouponServiceInterface::class => CouponService::class,
        InvoiceServiceInterface::class => InvoiceService::class,
        LoyaltyPointServiceInterface::class => LoyaltyPointService::class,
        NotificationServiceInterface::class => NotificationService::class,
        ProductVariantServiceInterface::class => ProductVariantService::class,
        PurchaseOrderServiceInterface::class => PurchaseOrderService::class,
        ServiceReviewServiceInterface::class => ServiceReviewService::class,
        StockTransferServiceInterface::class => StockTransferService::class,
        SupplierServiceInterface::class => SupplierService::class,
        TaxServiceInterface::class => TaxService::class,
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
