<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\BranchRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\Contracts\InventoryMovementRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Repositories\Contracts\ServiceCategoryRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Repositories\Eloquent\AppointmentRepository;
use App\Repositories\Eloquent\BranchRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\EmployeeRepository;
use App\Repositories\Eloquent\ExpenseRepository;
use App\Repositories\Eloquent\InventoryMovementRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\SaleRepository;
use App\Repositories\Eloquent\ServiceCategoryRepository;
use App\Repositories\Eloquent\ServiceRepository;
use App\Repositories\Eloquent\SettingRepository;
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
        AppointmentRepositoryInterface::class => AppointmentRepository::class,
        BranchRepositoryInterface::class => BranchRepository::class,
        CustomerRepositoryInterface::class => CustomerRepository::class,
        EmployeeRepositoryInterface::class => EmployeeRepository::class,
        ExpenseRepositoryInterface::class => ExpenseRepository::class,
        InventoryMovementRepositoryInterface::class => InventoryMovementRepository::class,
        PaymentRepositoryInterface::class => PaymentRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        SaleRepositoryInterface::class => SaleRepository::class,
        ServiceRepositoryInterface::class => ServiceRepository::class,
        ServiceCategoryRepositoryInterface::class => ServiceCategoryRepository::class,
        SettingRepositoryInterface::class => SettingRepository::class,
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
