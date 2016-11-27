<?php

namespace App\Notifications;

use App\Checklist;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ChecklistCompleteNotification extends Notification
{
    use Queueable;

    /**
     * @var Checklist
     */
    public $checklist;

    /**
     * Create a new notification instance.
     *
     * @param Checklist $checklist
     */
    public function __construct(Checklist $checklist)
    {
        $this->checklist = $checklist;
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
        $checklistName = $this->checklist->name;
        $checklistLink = env('APP_URL') . "/c/" . hashId('checklist', $this->checklist);
        return (new MailMessage)
            ->level('success')
            ->subject("Completed Checklist - {$checklistName}")
            ->line('<strong>Success!</strong> Received all the files you\'ve requested.')
            ->action('View Checklist', $checklistLink)
            ->line('Thanks for using ddocs and we hope to help you get more files again soon!');
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
