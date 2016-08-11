<?php

namespace App\Listeners;

use App\Events\NewUserSignedUp;
use App\Mailers\UserMailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailWelcomeMessage implements ShouldQueue
{
    /**
     * @var UserMailer
     */
    private $userMailer;

    /**
     * Create the event listener.
     *
     * @param UserMailer $userMailer
     */
    public function __construct(UserMailer $userMailer)
    {
        //
        $this->userMailer = $userMailer;
    }

    /**
     * Handle the event.
     *
     * @param  NewUserSignedUp  $event
     * @return void
     */
    public function handle(NewUserSignedUp $event)
    {
        $this->userMailer->sendWelcomeEmail($event->user);
    }
}
