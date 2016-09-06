<?php

namespace App\Mail;

use App\Checklist;
use App\Recipient;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LateFilesReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The Checklist that holds the late file.
     *
     * @var Checklist
     */
    public $checklist;

    /**
     * Collection of Checklist's late FileRequest(s).
     *
     * @var Collection
     */
    public $lateFiles;

    /**
     * DateTime now (carbon instance).
     *
     * @var static
     */
    public $today;

    /**
     * @var Recipient
     */
    public $recipient;

    /**
     * Create a new message instance.
     *
     * @param Recipient $recipient
     * @param Checklist $checklist
     */
    public function __construct(Recipient $recipient, Checklist $checklist)
    {
        $this->checklist = $checklist;
        $this->today = Carbon::now();
        $this->lateFiles = $checklist->requestedFiles()
                                ->where('status', '!=', 'received')
                               ->whereDate('due', '<', $this->today->format('Y-m-d'))
                               ->get();
        $this->recipient = $recipient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Overdue Files: ' . $this->checklist->name;
        $view = 'emails.files.reminder-late';
        return $this->subject($subject)->view($view);
    }
}
