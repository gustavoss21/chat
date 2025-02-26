<?php

namespace App\Listeners;

use App\Events\MessageViewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Queue\InteractsWithQueue;

class SendMessageViewedNotification implements ShouldQueueAfterCommit
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
    public function handle(MessageViewed $event): void
    {
        //
    }
}
