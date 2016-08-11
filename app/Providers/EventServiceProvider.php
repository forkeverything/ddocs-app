<?php

namespace App\Providers;

use App\Events\ChecklistCompleted;
use App\Events\ChecklistCreated;
use App\Events\NewUserSignedUp;
use App\Listeners\EmailChecklistCompleteNotification;
use App\Listeners\EmailRecipientOfNewChecklist;
use App\Listeners\EmailWelcomeMessage;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
        NewUserSignedUp::class => [
            EmailWelcomeMessage::class
        ],
        ChecklistCreated::class => [
            EmailRecipientOfNewChecklist::class
        ],
        ChecklistCompleted::class => [
            EmailChecklistCompleteNotification::class
        ]
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
