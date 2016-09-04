<?php

namespace App\Mail;

use App\Checklist;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChecklistComplete extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The Checklist that was completed.
     *
     * @var Checklist
     */
    public $checklist;

    /**
     * The user that made the Checklist.
     *
     * @var User
     */
    public $maker;

    /**
     * Create a new message instance.
     *
     * @param Checklist $checklist
     */
    public function __construct(Checklist $checklist)
    {
        $this->checklist = $checklist;
        $this->maker = $checklist->user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Received all files for: ' . $this->checklist->name;
        return $this->subject($subject)
                    ->view('emails.checklist.complete');
    }
}
