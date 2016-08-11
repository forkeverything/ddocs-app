<?php

namespace App\Events;

use App\Events\Event;
use App\FileRequest;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FileWasUploaded extends Event
{
    use SerializesModels;

    /**
     * @var FileRequest
     */
    public $fileRequest;

    /**
     * Create a new event instance.
     *
     * @param FileRequest $fileRequest
     */
    public function __construct(FileRequest $fileRequest)
    {
        $this->fileRequest = $fileRequest;
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
