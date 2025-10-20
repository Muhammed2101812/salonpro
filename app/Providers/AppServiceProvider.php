<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\AppointmentCancellationReasonRepositoryInterface;
use App\Repositories\Contracts\AppointmentCancellationRepositoryInterface;
use App\Repositories\Contracts\AppointmentConflictRepositoryInterface;
use App\Repositories\Contracts\AppointmentGroupParticipantRepositoryInterface;
use App\Repositories\Contracts\AppointmentGroupRepositoryInterface;
use App\Repositories\Contracts\AppointmentRecurrenceRepositoryInterface;
use App\Repositories\Contracts\AppointmentReminderRepositoryInterface;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\AppointmentWaitlistRepositoryInterface;
use App\Repositories\Contracts\BranchRepositoryInterface;
use App\Repositories\Contracts\BranchSettingRepositoryInterface;
use App\Repositories\Contracts\CampaignStatisticRepositoryInterface;
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
use App\Repositories\Contracts\InventoryMovementRepositoryInterface;
use App\Repositories\Contracts\NotificationCampaignRepositoryInterface;
use App\Repositories\Contracts\NotificationLogRepositoryInterface;
use App\Repositories\Contracts\NotificationPreferenceRepositoryInterface;
use App\Repositories\Contracts\NotificationQueueRepositoryInterface;
use App\Repositories\Contracts\NotificationTemplateRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\ProductBundleRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\PurchaseOrderItemRepositoryInterface;
use App\Repositories\Contracts\PurchaseOrderRepositoryInterface;
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
use App\Repositories\Eloquent\AppointmentCancellationReasonRepository;
use App\Repositories\Eloquent\AppointmentCancellationRepository;
use App\Repositories\Eloquent\AppointmentConflictRepository;
use App\Repositories\Eloquent\AppointmentGroupParticipantRepository;
use App\Repositories\Eloquent\AppointmentGroupRepository;
use App\Repositories\Eloquent\AppointmentRecurrenceRepository;
use App\Repositories\Eloquent\AppointmentReminderRepository;
use App\Repositories\Eloquent\AppointmentRepository;
use App\Repositories\Eloquent\AppointmentWaitlistRepository;
use App\Repositories\Eloquent\BranchRepository;
use App\Repositories\Eloquent\BranchSettingRepository;
use App\Repositories\Eloquent\CampaignStatisticRepository;
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
use App\Repositories\Eloquent\InventoryMovementRepository;
use App\Repositories\Eloquent\NotificationCampaignRepository;
use App\Repositories\Eloquent\NotificationLogRepository;
use App\Repositories\Eloquent\NotificationPreferenceRepository;
use App\Repositories\Eloquent\NotificationQueueRepository;
use App\Repositories\Eloquent\NotificationTemplateRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\ProductBundleRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\PurchaseOrderItemRepository;
use App\Repositories\Eloquent\PurchaseOrderRepository;
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
        AppointmentCancellationReasonRepositoryInterface::class => AppointmentCancellationReasonRepository::class,
        AppointmentCancellationRepositoryInterface::class => AppointmentCancellationRepository::class,
        AppointmentConflictRepositoryInterface::class => AppointmentConflictRepository::class,
        AppointmentGroupParticipantRepositoryInterface::class => AppointmentGroupParticipantRepository::class,
        AppointmentGroupRepositoryInterface::class => AppointmentGroupRepository::class,
        AppointmentRecurrenceRepositoryInterface::class => AppointmentRecurrenceRepository::class,
        AppointmentReminderRepositoryInterface::class => AppointmentReminderRepository::class,
        AppointmentRepositoryInterface::class => AppointmentRepository::class,
        AppointmentWaitlistRepositoryInterface::class => AppointmentWaitlistRepository::class,
        BranchRepositoryInterface::class => BranchRepository::class,
        BranchSettingRepositoryInterface::class => BranchSettingRepository::class,
        CampaignStatisticRepositoryInterface::class => CampaignStatisticRepository::class,
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
        InventoryMovementRepositoryInterface::class => InventoryMovementRepository::class,
        NotificationCampaignRepositoryInterface::class => NotificationCampaignRepository::class,
        NotificationLogRepositoryInterface::class => NotificationLogRepository::class,
        NotificationPreferenceRepositoryInterface::class => NotificationPreferenceRepository::class,
        NotificationQueueRepositoryInterface::class => NotificationQueueRepository::class,
        NotificationTemplateRepositoryInterface::class => NotificationTemplateRepository::class,
        PaymentRepositoryInterface::class => PaymentRepository::class,
        ProductBundleRepositoryInterface::class => ProductBundleRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        PurchaseOrderItemRepositoryInterface::class => PurchaseOrderItemRepository::class,
        PurchaseOrderRepositoryInterface::class => PurchaseOrderRepository::class,
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
