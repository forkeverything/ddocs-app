<?php

namespace App\Events;

use App\FileRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FileWasUploaded
{
    use InteractsWithSockets, SerializesModels;

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
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
