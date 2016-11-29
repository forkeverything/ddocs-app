<?php

namespace App\Listeners;

use App\Events\NewUserSignedUp;
use App\Mail\Welcome;
use CS_REST_Transactional_SmartEmail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class EmailWelcomeMessage
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
     * @param  NewUserSignedUp  $event
     * @return void
     */
    public function handle(NewUserSignedUp $event)
    {
        Mail::to($event->user)->send(new Welcome($event->user));

        // Send beta welcome
        $key = env('CAMPAIGN_MONITOR_KEY');
        $id = '5e2db2b9-c3bf-46fd-9438-b07b59e566b6';
        $wrap = new CS_REST_Transactional_SmartEmail($id, $key);
        $recipient = "{$event->user->name} <{$event->user->email}>";
        $message = ['To' => $recipient];
        $result = $wrap->send($message);
    }
}
