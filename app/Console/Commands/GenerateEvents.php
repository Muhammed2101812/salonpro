<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateEvents extends Command
{
    protected $signature = 'make:events';

    protected $description = 'Generate Event, Listener, and Job classes for business logic';

    public function handle(): int
    {
        $this->info("Generating Events, Listeners, and Jobs...");

        // Core business events
        $events = [
            // Appointment Events
            'AppointmentCreated' => ['SendAppointmentConfirmation', 'NotifyEmployee', 'CheckForConflicts'],
            'AppointmentUpdated' => ['SendRescheduledNotification', 'UpdateCalendar'],
            'AppointmentCancelled' => ['SendCancellationNotification', 'RefundPayment', 'UpdateAvailability'],
            'AppointmentCompleted' => ['GenerateInvoice', 'RequestReview', 'UpdateLoyaltyPoints'],
            'AppointmentNoShow' => ['NotifyManagement', 'ApplyNoShowPenalty'],

            // Customer Events
            'CustomerCreated' => ['SendWelcomeEmail', 'AssignToSegment'],
            'CustomerUpdated' => ['UpdateSegmentation'],
            'LoyaltyPointsEarned' => ['NotifyCustomer', 'CheckForRewards'],

            // Employee Events
            'EmployeeCreated' => ['SendEmployeeWelcome', 'SetupEmployeeAccount'],
            'ShiftAssigned' => ['NotifyEmployee'],
            'CommissionEarned' => ['NotifyEmployee', 'UpdatePayroll'],

            // Payment Events
            'PaymentReceived' => ['GenerateReceipt', 'UpdateAccountBalance'],
            'PaymentFailed' => ['NotifyCustomer', 'RetryPayment'],
            'RefundIssued' => ['NotifyCustomer', 'UpdateAccounting'],

            // Inventory Events
            'StockLevelLow' => ['NotifyManager', 'CreatePurchaseOrder'],
            'ProductReceived' => ['UpdateInventory', 'NotifyBranch'],

            // Marketing Events
            'CampaignSent' => ['TrackDelivery', 'UpdateStatistics'],
            'CouponUsed' => ['ValidateCoupon', 'ApplyDiscount'],
        ];

        $progressBar = $this->output->createProgressBar(count($events));

        foreach ($events as $eventName => $listeners) {
            $this->generateEvent($eventName);

            foreach ($listeners as $listenerName) {
                $this->generateListener($listenerName, $eventName);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        // Generate common jobs
        $this->generateCommonJobs();

        $this->newLine();
        $this->info("✅ Events, Listeners, and Jobs generated successfully!");
        $this->info("Don't forget to register them in EventServiceProvider!");

        return self::SUCCESS;
    }

    protected function generateEvent(string $eventName): void
    {
        $eventPath = app_path("Events/{$eventName}.php");

        if (File::exists($eventPath)) {
            return;
        }

        File::ensureDirectoryExists(app_path('Events'));

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class {$eventName}
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public mixed \$data
    ) {}
}

PHP;

        File::put($eventPath, $stub);
    }

    protected function generateListener(string $listenerName, string $eventName): void
    {
        $listenerPath = app_path("Listeners/{$listenerName}.php");

        if (File::exists($listenerPath)) {
            return;
        }

        File::ensureDirectoryExists(app_path('Listeners'));

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\\{$eventName};
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class {$listenerName} implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle({$eventName} \$event): void
    {
        // Handle the event
    }
}

PHP;

        File::put($listenerPath, $stub);
    }

    protected function generateCommonJobs(): void
    {
        $jobs = [
            'SendEmailJob',
            'SendSmsJob',
            'SendPushNotificationJob',
            'ProcessPaymentJob',
            'GenerateReportJob',
            'BackupDatabaseJob',
            'SyncInventoryJob',
            'CalculateCommissionsJob',
            'ProcessRecurringAppointmentsJob',
            'CleanupExpiredDataJob',
        ];

        $this->info("Creating " . count($jobs) . " common jobs...");
        $progressBar = $this->output->createProgressBar(count($jobs));

        foreach ($jobs as $jobName) {
            $this->generateJob($jobName);
            $progressBar->advance();
        }

        $progressBar->finish();
    }

    protected function generateJob(string $jobName): void
    {
        $jobPath = app_path("Jobs/{$jobName}.php");

        if (File::exists($jobPath)) {
            return;
        }

        File::ensureDirectoryExists(app_path('Jobs'));

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class {$jobName} implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public mixed \$data
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Job logic here
    }
}

PHP;

        File::put($jobPath, $stub);
    }
}
