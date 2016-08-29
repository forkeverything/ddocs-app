<?php

namespace App\Listeners;

use App\Events\CreatedUserFromEmailWebhook;
use App\Mail\WelcomeWithGeneratedPassword;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class EmailWelcomeMessageAndPassword
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
     * @param  CreatedUserFromEmailWebhook  $event
     * @return void
     */
    public function handle(CreatedUserFromEmailWebhook $event)
    {
        Mail::to($event->user)->send(new WelcomeWithGeneratedPassword($event->user, $event->password));
    }
}
