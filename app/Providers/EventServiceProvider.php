<?php

namespace App\Providers;

use App\Events\ChecklistCompleted;
use App\Events\ChecklistCreated;
use App\Events\CreatedUserFromEmailWebhook;
use App\Events\FileWasRejected;
use App\Events\FileWasUploaded;
use App\Events\NewUserSignedUp;
use App\Events\RecipientClaimedInvitation;
use App\Events\UserHasRunOutOfCredits;
use App\Listeners\SendChecklistCompleteNotification;
use App\Listeners\SendReceivedFreeCreditsNotification;
use App\Listeners\SendFileChangesRequiredNotification;
use App\Listeners\SendNotEnoughCreditsNotification;
use App\Listeners\SendNewChecklistNotification;
use App\Listeners\EmailWelcomeMessage;
use App\Listeners\EmailWelcomeMessageAndPassword;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        NewUserSignedUp::class => [
            EmailWelcomeMessage::class
        ],
        ChecklistCreated::class => [
            SendNewChecklistNotification::class
        ],
        FileWasUploaded::class => [
            // Do something
        ],
        ChecklistCompleted::class => [
            SendChecklistCompleteNotification::class
        ],
        FileWasRejected::class => [
            SendFileChangesRequiredNotification::class
        ],
        UserHasRunOutOfCredits::class => [
            SendNotEnoughCreditsNotification::class
        ],
        RecipientClaimedInvitation::class => [
            SendReceivedFreeCreditsNotification::class
        ],
        CreatedUserFromEmailWebhook::class => [
            EmailWelcomeMessageAndPassword::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
