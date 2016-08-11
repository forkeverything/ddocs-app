<?php

namespace App\Listeners;

use App\Events\ChecklistCompleted;
use App\Mailers\ChecklistMailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailChecklistCompleteNotification implements ShouldQueue
{
    /**
     * @var ChecklistMailer
     */
    private $checklistMailer;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ChecklistMailer $checklistMailer)
    {
        //
        $this->checklistMailer = $checklistMailer;
    }

    /**
     * Handle the event.
     *
     * @param  ChecklistCompleted  $event
     * @return void
     */
    public function handle(ChecklistCompleted $event)
    {
        $this->checklistMailer->sendChecklistCompleteEmail($event->checklist);
    }
}
