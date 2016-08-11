<?php

namespace App\Jobs;

use App\Checklist;
use App\Jobs\Job;
use App\Mailers\ChecklistMailer;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class SendUpcomingDueFileReminders extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @param ChecklistMailer $checklistMailer
     */
    public function handle(ChecklistMailer $checklistMailer)
    {
        $checklists = $this->fetchChecklistsWithFilesDueIn3Days();
        foreach ($checklists as $checklist) {
            $checklistMailer->sendUpcomingFilesReminder($checklist);
        }
    }

    /**
     * Eloquent query to get relevant Checklists, ones that have File Request(s)
     * that are due exactly 3 days from today.
     *
     * @return mixed
     */
    protected function fetchChecklistsWithFilesDueIn3Days()
    {
        return  Checklist::whereExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('file_requests')
                  ->where('status', '!=', 'received')
                  ->whereDate('due', '=', Carbon::now()->addDays(4)->format('Y-m-d'))
                  ->whereRaw('checklist_id = checklists.id');
        })->get()->unique();
    }
}
