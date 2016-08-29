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
        Mail::to($event->checklist->recipient)->send(new NewChecklist($event->checklist));
    }
}
