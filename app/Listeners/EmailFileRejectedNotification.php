<?php

namespace App\Listeners;

use App\Events\FileWasRejected;
use App\Mailers\FileRequestMailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailFileRejectedNotification implements ShouldQueue
{
    /**
     * @var FileRequestMailer
     */
    private $fileRequestMailer;

    /**
     * Create the event listener.
     *
     * @param FileRequestMailer $fileRequestMailer
     */
    public function __construct(FileRequestMailer $fileRequestMailer)
    {
        $this->fileRequestMailer = $fileRequestMailer;
    }

    /**
     * Handle the event.
     *
     * @param  FileWasRejected  $event
     * @return void
     */
    public function handle(FileWasRejected $event)
    {
        $this->fileRequestMailer->sendChangesRequiredEmail($event->fileRequest);
    }
}
