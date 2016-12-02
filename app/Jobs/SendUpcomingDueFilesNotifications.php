<?php

namespace App\Jobs;

use App\Checklist;
use App\Notifications\UpcomingFilesNotification;
use App\Utilities\Traits\SendsRecipientNotifications;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendUpcomingDueFilesNotifications implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, SendsRecipientNotifications;

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
        $checklists = $this->fetchChecklistsWithFilesDueIn3Days();
        foreach ($checklists as $checklist) {
            foreach($checklist->recipients as $recipient) {
                $target = $recipient;
                $this->attemptLinkRecipientToUser($recipient);
                if($registeredUser = $recipient->user) $target = $registeredUser;
                $target->notify(new UpcomingFilesNotification($recipient, $checklist));
            }
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
                  ->whereDate('due', '=', Carbon::now()->addDays(3)->format('Y-m-d'))
                  ->whereRaw('checklist_id = checklists.id');
        })->get()->unique();
    }
}
