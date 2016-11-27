<?php

namespace App\Notifications;

use App\Checklist;
use App\Recipient;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewChecklistNotification extends Notification
{
    use Queueable;

    /**
     * @var Checklist
     */
    public $checklist;

    /**
     * @var Recipient
     */
    public $recipient;

    /**
     * Create a new notification instance.
     *
     * @param Checklist $checklist
     * @param Recipient $recipient
     */
    public function __construct(Checklist $checklist, Recipient $recipient)
    {
        $this->checklist = $checklist;
        $this->recipient = $recipient;
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
        $checklistLink = env('APP_URL') . "/c/" . hashId('checklist', $this->checklist);
        $turnOffNotificationsLink = env('APP_URL') . "/recipients/" . hashId('recipient', $this->recipient) . "/turn_off_notifications";

        return (new MailMessage)
            ->subject("Files for {$this->checklist->user->name}")
            ->line("You've got a new checklist from {$this->checklist->user->name} ({$this->checklist->user->email}). To upload files, view the checklist by clicking the button below.")
            ->action('View Checklist', $checklistLink)
            ->line("If there are any files with due dates, we'll remind you before they're due, as well as when they are late. We'll also automatically notify you of any changes requested and comments posted.")
            ->line("<a href='{$turnOffNotificationsLink}'>Click here to turn off notifications for this checklist</a>");
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
