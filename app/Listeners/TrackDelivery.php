<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\CampaignSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TrackDelivery implements ShouldQueue
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
    public function handle(CampaignSent $event): void
    {
        // Handle the event
    }
}
