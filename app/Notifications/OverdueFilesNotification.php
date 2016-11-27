<?php

namespace App\Notifications;

use App\Checklist;
use App\Recipient;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OverdueFilesNotification extends Notification
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
        $checklistMaker = $this->checklist->user->name;
        $checklistLink = env('APP_URL') . "/c/" . hashId('checklist', $this->checklist);
        $turnOffNotificationsLink = env('APP_URL') . "/recipients/" . hashId('recipient', $this->recipient) . "/turn_off_notifications";
        $lateFiles = '- ' . implode($this->checklist->requestedFiles()
                                                    ->where('status', '!=', 'received')
                                                    ->whereDate('due', '<', Carbon::today()->format('Y-m-d'))
                                                    ->get()
                                                    ->pluck('name')
                                                    ->toArray(), '<br>- ');

        return (new MailMessage)
            ->level('error')
            ->subject("Overdue Files - {$checklistName}")
            ->line("{$checklistMaker} is still expecting some files from you for <strong>{$checklistName}</strong>:")
            ->line($lateFiles)
            ->action('View Checklist', $checklistLink)
            ->line("Please upload the files as soon as you can. Alternatively, if you're done with the uploads, you can turn off notifications from the link below.")
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
