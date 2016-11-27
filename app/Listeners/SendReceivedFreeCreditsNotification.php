<?php

namespace App\Listeners;

use App\Events\RecipientClaimedInvitation;
use App\Mail\FreeCreditsReceived;
use App\Notifications\ReceivedFreeCreditsNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendReceivedFreeCreditsNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RecipientClaimedInvitation  $event
     * @return void
     */
    public function handle(RecipientClaimedInvitation $event)
    {
        $event->checklist->user->notify(new ReceivedFreeCreditsNotification($event->recipient));
    }
}
