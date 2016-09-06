<?php

namespace App\Listeners;

use App\Events\ChecklistCreated;
use App\Mail\NewChecklist;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class EmailRecipientOfNewChecklist
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
     * @param  ChecklistCreated  $event
     * @return void
     */
    public function handle(ChecklistCreated $event)
    {
        foreach ($event->checklist->recipients as $recipient) {
            Mail::to($recipient->email)->send(new NewChecklist($recipient, $event->checklist));
        }
    }
}
