<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\StockLevelLow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreatePurchaseOrder implements ShouldQueue
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
    public function handle(StockLevelLow $event): void
    {
        // Handle the event
    }
}
