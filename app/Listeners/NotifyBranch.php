<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ProductReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyBranch implements ShouldQueue
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
    public function handle(ProductReceived $event): void
    {
        // Handle the event
    }
}
