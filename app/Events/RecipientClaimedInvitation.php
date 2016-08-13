<?php

namespace App\Events;

use App\Checklist;
use App\Events\Event;
use App\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RecipientClaimedInvitation extends Event
{
    use SerializesModels;

    /**
     * @var Checklist
     */
    public $checklist;
    /**
     * @var User
     */
    public $recipient;

    /**
     * Create a new event instance.
     *
     * @param Checklist $checklist
     * @param User $recipient
     */
    public function __construct(Checklist $checklist, User $recipient)
    {
        $this->checklist = $checklist;
        $this->recipient = $recipient;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
