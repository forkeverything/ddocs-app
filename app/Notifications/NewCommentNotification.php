<?php

namespace App\Notifications;

use App\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewCommentNotification extends Notification
{
    use Queueable;

    /**
     * @var Comment
     */
    public $comment;

    /**
     * Create a new notification instance.
     *
     * @param Comment $comment
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
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
        if (class_basename($notifiable) === "User" || $notifiable->receive_notification_emails) $channels[] = 'mail';
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
        $signUpLink = env('APP_URL') . "/register";
        $sender = $this->comment->sender;
        $subject = $this->comment->subject;
        $subjectClass = class_basename($subject);
        $commentLink = '';
        $turnOffNotificationsLink = '';
        if ($subjectClass === "FileRequest") {
            $commentLink = env('APP_URL') . "/c/" . $subject->checklist->hash;
            $turnOffNotificationsLink = env('APP_URL') . "/recipients/" . $notifiable->hash . "/turn_off_notifications";
        }
        if ($subjectClass === "ProjectFile") $commentLink = env('APP_URL') . "/projects/" . $subject->folder->project_id;

        $mailMessage = new MailMessage;

        $mailMessage
            ->subject("{$this->comment->sender->name} commented on {$subject->name}")
            ->from("comment_{$this->comment->hash}@my.ddocs.com", "ddocs")
                    ->line("<strong>{$sender->name}</strong>({$sender->email}) commented on {$subject->name}:")
                    ->line('"' . $this->comment->body . '"')
                    ->action('View Comment', $commentLink)
                    ->line("<em>If you have an account with us at this email address, you can reply directly to this email and it'll be posted as a reply comment. Another awesome reason to <a href='" . $signUpLink . "'>sign up</a> for an account.</em>");

        if ($subjectClass === "FileRequest") $mailMessage->line("<a href='{$turnOffNotificationsLink}'>Click here to turn off notifications for this checklist</a>");

        return $mailMessage;

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
