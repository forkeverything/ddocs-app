<?php

namespace App\Events;

use App\Checklist;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChecklistCompleted extends Event
{
    use SerializesModels;

    /**
     * @var Checklist
     */
    public $checklist;

    /**
     * Create a new event instance.
     *
     * @param Checklist $checklist
     */
    public function __construct(Checklist $checklist)
    {
        $this->checklist = $checklist;
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
