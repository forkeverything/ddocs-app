<?php

namespace App\Listeners;

use App\Events\FileWasRejected;
use App\Mail\FileChangesRequired;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class EmailFileRejectedNotification
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
     * @param  FileWasRejected  $event
     * @return void
     */
    public function handle(FileWasRejected $event)
    {
        Mail::to($event->fileRequest->checklist->user)->send(new FileChangesRequired($event->fileRequest));
    }
}
