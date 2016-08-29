<?php

namespace App\Mail;

use App\Checklist;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpcomingFilesReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The Checklist that contains upcoming due files.
     *
     * @var Checklist
     */
    public $checklist;

    /**
     * Collection of upcoming FileRequest(s).
     *
     * @var
     */
    public $upcomingFiles;

    /**
     * Create a new message instance.
     *
     * @param Checklist $checklist
     */
    public function __construct(Checklist $checklist)
    {
        $this->checklist = $checklist;
        $this->upcomingFiles = $this->checklist->requestedFiles()
                                         ->whereDate('due', '=', Carbon::now()->addDays(4)->format('Y-m-d'))
                                         ->get();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Files Collector - ' . 'Due Files Coming Up';
        $view = 'emails.files.reminder-upcoming';
        return $this->subject($subject)->view($view);
    }
}
