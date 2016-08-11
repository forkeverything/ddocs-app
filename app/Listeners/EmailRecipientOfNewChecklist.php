<?php

namespace App\Listeners;

use App\Events\ChecklistCreated;
use App\Mailers\ChecklistMailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailRecipientOfNewChecklist implements ShouldQueue
{
    /**
     * @var ChecklistMailer
     */
    private $checklistMailer;

    /**
     * Create the event listener.
     *
     * @param ChecklistMailer $checklistMailer
     */
    public function __construct(ChecklistMailer $checklistMailer)
    {
        $this->checklistMailer = $checklistMailer;
    }

    /**
     * Handle the event.
     *
     * @param  ChecklistCreated  $event
     * @return void
     */
    public function handle(ChecklistCreated $event)
    {
        $this->checklistMailer->sendNewChecklistNotificationEmail($event->checklist);
    }
}
