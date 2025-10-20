<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\RefundIssued;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateAccounting implements ShouldQueue
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
    public function handle(RefundIssued $event): void
    {
        // Handle the event
    }
}
