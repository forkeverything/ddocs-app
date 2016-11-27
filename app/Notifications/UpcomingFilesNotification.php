<?php

namespace App\Notifications;

use App\Checklist;
use App\Recipient;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UpcomingFilesNotification extends Notification
{
    use Queueable;

    /**
     * @var Recipient
     */
    public $recipient;
    /**
     * @var Checklist
     */
    public $checklist;

    /**
     * Create a new notification instance.
     *
     * @param Recipient $recipient
     * @param Checklist $checklist
     */
    public function __construct(Recipient $recipient, Checklist $checklist)
    {
        $this->recipient = $recipient;
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
        $channels = [];
        if ($this->recipient->receive_notification_emails) $channels[] = 'mail';
//        if (class_basename($notifiable) === 'User') $channels[] = 'database';
        return $channels;
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
        $turnOffNotificationsLink = env('APP_URL') . "/recipients/" . hashId('recipient', $this->recipient) . "/turn_off_notifications";
        $upcomingFiles = '- ' . implode($this->checklist->requestedFiles()
                                                        ->whereDate('due', '=', Carbon::now()->addDays(4)->format('Y-m-d'))
                                                        ->get()
                                                        ->pluck('name')
                                                        ->toArray(), '<br>- ');

        return (new MailMessage)
            ->subject("Upcoming Files - {$checklistName}")
            ->line("<strong>{$checklistName}</strong>")
            ->line("You have some files that need uploading soon for the above checklist.")
            ->line($upcomingFiles)
            ->action('View Checklist', $checklistLink)
            ->line("Whenever you're ready, upload the files on the checklist page.")
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
            'checklist' => $this->checklist->name
        ];
    }
}
