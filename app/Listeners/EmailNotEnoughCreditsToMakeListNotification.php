<?php

namespace App\Listeners;

use App\Events\UserHasRunOutOfCredits;
use App\Mail\NotEnoughCreditsForList;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class EmailNotEnoughCreditsToMakeListNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserHasRunOutOfCredits  $event
     * @return void
     */
    public function handle(UserHasRunOutOfCredits $event)
    {
        Mail::to($event->user)->send(new NotEnoughCreditsForList($event->user));
    }
}
