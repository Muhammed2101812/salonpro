<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\InventoryMovement;
use App\Models\Invoice;
use App\Models\LoyaltyProgram;
use App\Models\MarketingCampaign;
use App\Models\Payment;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\Service;
use App\Models\StockAudit;
use App\Models\SystemSetting;
use App\Policies\AppointmentPolicy;
use App\Policies\BranchPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\ExpensePolicy;
use App\Policies\InventoryPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\LoyaltyProgramPolicy;
use App\Policies\MarketingCampaignPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProductPolicy;
use App\Policies\PurchaseOrderPolicy;
use App\Policies\SalePolicy;
use App\Policies\ServicePolicy;
use App\Policies\StockAuditPolicy;
use App\Policies\SystemSettingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Appointment::class => AppointmentPolicy::class,
        Branch::class => BranchPolicy::class,
        Customer::class => CustomerPolicy::class,
        Employee::class => EmployeePolicy::class,
        Expense::class => ExpensePolicy::class,
        InventoryMovement::class => InventoryPolicy::class,
        Invoice::class => InvoicePolicy::class,
        LoyaltyProgram::class => LoyaltyProgramPolicy::class,
        MarketingCampaign::class => MarketingCampaignPolicy::class,
        Payment::class => PaymentPolicy::class,
        Product::class => ProductPolicy::class,
        PurchaseOrder::class => PurchaseOrderPolicy::class,
        Sale::class => SalePolicy::class,
        Service::class => ServicePolicy::class,
        StockAudit::class => StockAuditPolicy::class,
        SystemSetting::class => SystemSettingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
