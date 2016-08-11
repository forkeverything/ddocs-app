<?php

namespace App\Listeners;

use App\Events\ChecklistCompleted;
use App\Events\Event;
use App\Events\FileWasUploaded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckIfChecklistIsComplete implements ShouldQueue
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
     * @param  FileWasUploaded  $event
     * @return void
     */
    public function handle(FileWasUploaded $event)
    {
        $requiredFiles = $event->fileRequest->checklist->requestedFiles()->where('required', 1)->get();

        $totalRequired = $requiredFiles->count();
        $numCompleted = $requiredFiles->where('status', 'received')->count();

        // If we received all compulsory files
        if($numCompleted === $totalRequired) Event::fire(new ChecklistCompleted($event->fileRequest->checklist));
    }
}
