<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotEnoughCreditsForList extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The User that ran out of credits.
     *
     * @var User
     */
    public $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'You\'ve Ran Out Of Credits';
        $view = 'emails.user.not-enough-credits';
        return $this->subject($subject)->view($view);
    }
}
