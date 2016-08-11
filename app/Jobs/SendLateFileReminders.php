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

class SendLateFileReminders extends Job implements ShouldQueue
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
        $checklists = $this->fetchChecklistsWithLateFiles();
        foreach ($checklists as $checklist) {
            $checklistMailer->sendLateFilesReminder($checklist);
        }
    }

    /**
     * Eloquent query to get checklists that have at least 1 late file.
     *
     * @return mixed
     */
    protected function fetchChecklistsWithLateFiles()
    {
        return Checklist::whereExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('file_requests')
                  ->where('status', '!=', 'received')
                ->whereDate('due', '<', Carbon::now()->format('Y-m-d'))
                  ->whereRaw('checklist_id = checklists.id');
        })->get()->unique();
    }
}
