<?php

namespace App\Events;

use App\Checklist;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RecipientClaimedInvitation
{
    use InteractsWithSockets, SerializesModels;

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
     * @var User
     */
    private $user;

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
