<?php

namespace App\Listeners;

use App\Events\ChecklistCompleted;
use App\Mail\ChecklistComplete;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendChecklistCompleteNotification
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
        $event->checklist->user->sendChecklistCompleteNotification($event->checklist);
    }
}
