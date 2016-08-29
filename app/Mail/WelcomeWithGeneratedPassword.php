<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeWithGeneratedPassword extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The User that signed up via. email.
     *
     * @var
     */
    public $user;

    /**
     * The random generated password.
     *
     * @var
     */
    public $password;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param $password
     */
    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Files Collector - Account Created';
        $view = 'emails.user.welcome-password';
        return $this->subject($subject)->view($view);
    }
}
