<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotEnoughCreditsNotification extends Notification
{
    use Queueable;

    /**
     * @var User
     */
    public $user;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subscribeUrl = env('APP_URL') . '/account';
        return (new MailMessage)
            ->level('success')
            ->greeting("Checklist not created!")
            ->subject("Not Enough Credits")
            ->line("We couldn't create your last checklist because you've ran out of credits. To continue using ddocs you'll need to do one of the following:")
            ->line("<strong>Wait</strong><br>Every month you get 5 free credits.")
            ->line("<strong>Invite</strong><br>Ask any of your recipients to create an account from their checklist page and you'll both get free credits.")
            ->line("<strong>Subscribe</strong><br>For $15/month, you'll get to make unlimited lists as well as up to 1TB storage.")
            ->action("Subcribe", $subscribeUrl)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
