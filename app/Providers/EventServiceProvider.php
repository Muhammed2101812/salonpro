<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\AppointmentCancelled;
use App\Events\AppointmentCompleted;
use App\Events\AppointmentCreated;
use App\Events\AppointmentNoShow;
use App\Events\AppointmentUpdated;
use App\Events\CampaignSent;
use App\Events\CommissionEarned;
use App\Events\CouponUsed;
use App\Events\CustomerCreated;
use App\Events\CustomerUpdated;
use App\Events\EmployeeCreated;
use App\Events\LoyaltyPointsEarned;
use App\Events\PaymentFailed;
use App\Events\PaymentReceived;
use App\Events\ProductReceived;
use App\Events\RefundIssued;
use App\Events\ShiftAssigned;
use App\Events\StockLevelLow;
use App\Listeners\ApplyDiscount;
use App\Listeners\ApplyNoShowPenalty;
use App\Listeners\AssignToSegment;
use App\Listeners\CheckForConflicts;
use App\Listeners\CheckForRewards;
use App\Listeners\CreatePurchaseOrder;
use App\Listeners\GenerateInvoice;
use App\Listeners\GenerateReceipt;
use App\Listeners\NotifyBranch;
use App\Listeners\NotifyCustomer;
use App\Listeners\NotifyEmployee;
use App\Listeners\NotifyManagement;
use App\Listeners\NotifyManager;
use App\Listeners\RequestReview;
use App\Listeners\RetryPayment;
use App\Listeners\SendAppointmentConfirmation;
use App\Listeners\SendCancellationNotification;
use App\Listeners\SendEmployeeWelcome;
use App\Listeners\SendRescheduledNotification;
use App\Listeners\SendWelcomeEmail;
use App\Listeners\SetupEmployeeAccount;
use App\Listeners\TrackDelivery;
use App\Listeners\UpdateAccounting;
use App\Listeners\UpdateAccountBalance;
use App\Listeners\UpdateAvailability;
use App\Listeners\UpdateCalendar;
use App\Listeners\UpdateInventory;
use App\Listeners\UpdateLoyaltyPoints;
use App\Listeners\UpdatePayroll;
use App\Listeners\UpdateSegmentation;
use App\Listeners\UpdateStatistics;
use App\Listeners\ValidateCoupon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Appointment Events
        AppointmentCreated::class => [
            SendAppointmentConfirmation::class,
            NotifyEmployee::class,
            CheckForConflicts::class,
        ],

        AppointmentUpdated::class => [
            SendRescheduledNotification::class,
            UpdateCalendar::class,
        ],

        AppointmentCancelled::class => [
            SendCancellationNotification::class,
            RefundPayment::class,
            UpdateAvailability::class,
        ],

        AppointmentCompleted::class => [
            GenerateInvoice::class,
            RequestReview::class,
            UpdateLoyaltyPoints::class,
        ],

        AppointmentNoShow::class => [
            NotifyManagement::class,
            ApplyNoShowPenalty::class,
        ],

        // Customer Events
        CustomerCreated::class => [
            SendWelcomeEmail::class,
            AssignToSegment::class,
        ],

        CustomerUpdated::class => [
            UpdateSegmentation::class,
        ],

        LoyaltyPointsEarned::class => [
            NotifyCustomer::class,
            CheckForRewards::class,
        ],

        // Employee Events
        EmployeeCreated::class => [
            SendEmployeeWelcome::class,
            SetupEmployeeAccount::class,
        ],

        ShiftAssigned::class => [
            NotifyEmployee::class,
        ],

        CommissionEarned::class => [
            NotifyEmployee::class,
            UpdatePayroll::class,
        ],

        // Payment Events
        PaymentReceived::class => [
            GenerateReceipt::class,
            UpdateAccountBalance::class,
        ],

        PaymentFailed::class => [
            NotifyCustomer::class,
            RetryPayment::class,
        ],

        RefundIssued::class => [
            NotifyCustomer::class,
            UpdateAccounting::class,
        ],

        // Inventory Events
        StockLevelLow::class => [
            NotifyManager::class,
            CreatePurchaseOrder::class,
        ],

        ProductReceived::class => [
            UpdateInventory::class,
            NotifyBranch::class,
        ],

        // Marketing Events
        CampaignSent::class => [
            TrackDelivery::class,
            UpdateStatistics::class,
        ],

        CouponUsed::class => [
            ValidateCoupon::class,
            ApplyDiscount::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
