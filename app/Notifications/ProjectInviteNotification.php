<?php

namespace App\Notifications;

use App\Project;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectInviteNotification extends Notification
{
    use Queueable;
    /**
     * @var Project
     */
    public $project;
    /**
     * @var User
     */
    public $inviter;

    /**
     * Create a new notification instance.
     *
     * @param Project $project
     * @param User $inviter
     */
    public function __construct(Project $project, User $inviter)
    {
        $this->project = $project;
        $this->inviter = $inviter;
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
        return (new MailMessage)
            ->level('success')
            ->subject("Project Invitation - {$this->project->name}")
            ->line("{$this->inviter->name}({$this->inviter->email}) has invited you to upload files to a project.")
            ->action('Accept Invitation', 'https://laravel.com')
            ->line('Joining a project requires you to create an account but don\'t worry, like other good things - it\'s free!');
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
