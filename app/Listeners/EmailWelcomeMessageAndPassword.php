<?php

namespace App\Listeners;

use App\Events\CreatedUserFromEmailWebhook;
use App\Mailers\UserMailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailWelcomeMessageAndPassword implements ShouldQueue
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

        $this->userMailer = $userMailer;
    }

    /**
     * Handle the event.
     *
     * @param  CreatedUserFromEmailWebhook  $event
     * @return void
     */
    public function handle(CreatedUserFromEmailWebhook $event)
    {
        $this->userMailer->sendWelcomeWithPasswordEmail($event->user, $event->password);
    }
}
