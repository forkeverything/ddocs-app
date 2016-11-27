<?php

namespace App\Notifications;

use App\FileRequest;
use App\Recipient;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class FileChangesRequiredNotification extends Notification
{
    use Queueable;
    /**
     * @var Recipient
     */
    public $recipient;
    /**
     * @var FileRequest
     */
    public $fileRequest;

    /**
     * Create a new notification instance.
     *
     * @param Recipient $recipient
     * @param FileRequest $fileRequest
     */
    public function __construct(Recipient $recipient, FileRequest $fileRequest)
    {
        $this->recipient = $recipient;
        $this->fileRequest = $fileRequest;
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
        $checklistMaker = $this->fileRequest->checklist->user->name;
        $fileName = $this->fileRequest->name;
        $changesRequired = $this->fileRequest->uploads->last()->rejected_reason;
        $checklistLink = env('APP_URL') . "/c/" . hashId('checklist', $this->fileRequest->checklist);
        $turnOffNotificationsLink = env('APP_URL') . "/recipients/" . hashId('recipient', $this->recipient) . "/turn_off_notifications";

        return (new MailMessage)
            ->subject("Changes Required - {$fileName}")
            ->line("<strong>{$fileName}</strong>")
            ->line("{$checklistMaker} has requested changes to be made to the above file.")
            ->line('<em>"' . $changesRequired . '"</em>')
            ->action('View Checklist', $checklistLink)
            ->line('After making changes, you can re-upload the file at the checklist page.')
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
            'file' => $this->fileRequest->name
        ];
    }
}
