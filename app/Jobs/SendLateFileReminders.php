<?php

namespace App\Jobs;

use App\Checklist;
use App\Mail\LateFilesReminder;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendLateFileReminders implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $checklists = $this->fetchChecklistsWithLateFiles();
        foreach ($checklists as $checklist) {
            if($checklist->recipient_notifications) Mail::to($checklist->recipient)->send(new LateFilesReminder($checklist));
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
