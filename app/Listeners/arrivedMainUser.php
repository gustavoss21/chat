<?php

namespace App\Listeners;

use App\Events\AlertsArrivalMainUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class arrivedMainUser
{
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
    public function handle(AlertsArrivalMainUser $event): void
    {
        //
    }
}
