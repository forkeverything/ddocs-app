<?php

namespace App\Jobs;

use App\Checklist;
use App\Events\ChecklistCompleted;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Event;

class CheckIfChecklistComplete implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The Checklist to check if complete. ie. all FileRequest(s)
     * fulfilled.
     *
     * @var Checklist
     */
    private $checklist;

    /**
     * Create a new job instance.
     *
     * @param Checklist $checklist
     */
    public function __construct(Checklist $checklist)
    {
        $this->checklist = $checklist;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $requiredFiles = $this->checklist->requestedFiles()->where('required', 1)->get();

        $totalRequired = $requiredFiles->count();
        $numCompleted = $requiredFiles->where('status', 'received')->count();

        // If we received all compulsory files
        if($numCompleted === $totalRequired) Event::fire(new ChecklistCompleted($this->checklist));
    }
}
