<?php

namespace App\Listeners;

use App\Events\UserHasRunOutOfCredits;
use App\Mailers\UserMailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailNotEnoughCreditsToMakeListNotification implements ShouldQueue
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
     * @param  UserHasRunOutOfCredits  $event
     * @return void
     */
    public function handle(UserHasRunOutOfCredits $event)
    {
        $this->userMailer->sendNotEnoughCreditsToMakeListEmail($event->user);
    }
}
