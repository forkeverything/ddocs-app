<?php

namespace App\Listeners;

use App\Events\ChecklistCompleted;
use App\Mail\ChecklistComplete;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class EmailChecklistCompleteNotification
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
     * @param  ChecklistCompleted  $event
     * @return void
     */
    public function handle(ChecklistCompleted $event)
    {
        Mail::to($event->checklist->user)->send(new ChecklistComplete($event->checklist));
    }
}
