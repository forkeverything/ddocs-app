<?php

namespace App\Mail;

use App\Checklist;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FreeCreditsReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The Checklist that earned the credits.
     *
     * @var Checklist
     */
    public $checklist;

    /**
     * The User that claimed the free credits.
     *
     * @var User
     */
    public $recipient;

    /**
     * Create a new message instance.
     *
     * @param Checklist $checklist
     * @param User $recipient
     */
    public function __construct(Checklist $checklist, User $recipient)
    {
        $this->checklist = $checklist;
        $this->recipient = $recipient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Free Credits From ' . $this->recipient->name;
        $view = 'emails.checklist.free-credits';
        return $this->subject($subject)
                    ->view($view);
    }
}
