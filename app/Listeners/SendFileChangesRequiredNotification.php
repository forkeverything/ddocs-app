<?php

namespace App\Listeners;

use App\Events\FileWasRejected;
use App\Notifications\FileChangesRequiredNotification;
use App\Utilities\Traits\SendsRecipientNotifications;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendFileChangesRequiredNotification
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
     * @param  FileWasRejected  $event
     * @return void
     */
    public function handle(FileWasRejected $event)
    {
        foreach ($event->fileRequest->checklist->recipients as $recipient) {
            $target = $recipient;
            $this->attemptLinkRecipientToUser($recipient);
            if($registeredUser = $recipient->user) $target = $registeredUser;
            $target->notify(new FileChangesRequiredNotification($recipient, $event->fileRequest));
        }
    }
}
