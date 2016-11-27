<?php

namespace App\Listeners;

use App\Events\ChecklistCreated;
use App\Mail\NewChecklist;
use App\Notifications\NewChecklistNotification;
use App\Utilities\Traits\SendsRecipientNotifications;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendNewChecklistNotification
{
    use SendsRecipientNotifications;

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
     * @param  ChecklistCreated  $event
     * @return void
     */
    public function handle(ChecklistCreated $event)
    {
        foreach ($event->checklist->recipients as $recipient) {
            $target = $recipient;
            $this->attemptLinkRecipientToUser($recipient);
            if($registeredUser = $recipient->user) $target = $registeredUser;
            $target->notify(new NewChecklistNotification($event->checklist, $recipient));
        }
    }
}
