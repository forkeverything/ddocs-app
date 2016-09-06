<?php

namespace App\Mail;

use App\Checklist;
use App\Recipient;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewChecklist extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Newly created Checklist.
     *
     * @var Checklist
     */
    public $checklist;

    /**
     * The User that made the Checklist.
     *
     * @var User
     */
    public $maker;

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
        $this->maker = $checklist->user;
        $this->recipient = $recipient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Documents request from ' . $this->maker->name;
        $view = 'emails.checklist.new';

        return $this->subject($subject)->view($view);
    }
}
