<?php

namespace App\Listeners;

use App\Events\RecipientClaimedInvitation;
use App\Mail\FreeCreditsReceived;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class EmailChecklistOwnerFreeCreditsFromRecipient
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
        Mail::to($event->checklist->user)->send(new FreeCreditsReceived($event->checklist, $event->recipient));
    }
}
