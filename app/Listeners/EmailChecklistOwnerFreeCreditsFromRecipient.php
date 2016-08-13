<?php

namespace App\Listeners;

use App\Events\RecipientClaimedInvitation;
use App\Mailers\ChecklistMailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailChecklistOwnerFreeCreditsFromRecipient
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
     * @param  RecipientClaimedInvitation  $event
     * @return void
     */
    public function handle(RecipientClaimedInvitation $event)
    {
        $this->checklistMailer->sendFreeCreditsReceived($event->checklist, $event->recipient);
    }
}
