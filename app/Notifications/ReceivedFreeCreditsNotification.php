<?php

namespace App\Notifications;

use App\Checklist;
use App\Recipient;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReceivedFreeCreditsNotification extends Notification
{
    use Queueable;

    /**
     * @var Recipient
     */
    public $userRecipient;

    /**
     * Create a new notification instance.
     *
     * @param User $userRecipient
     */
    public function __construct(User $userRecipient)
    {
        $this->userRecipient = $userRecipient;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $userRecipientName = $this->userRecipient->name;
        return (new MailMessage)
            ->subject("Free Credits - {$userRecipientName}")
            ->line("<strong>10 Free Credits!</strong>")
            ->line("{$userRecipientName} ({$this->userRecipient->email}) has signed up and scored you both bonus credits. It's our way of saying thanks for sharing the love.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
