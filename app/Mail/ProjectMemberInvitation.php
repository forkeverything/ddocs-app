<?php

namespace App\Mail;

use App\Project;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectMemberInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    public $admin;

    /**
     * @var Project
     */
    public $project;

    /**
     * Create a new message instance.
     *
     * @param User $admin
     * @param Project $project
     */
    public function __construct(User $admin, Project $project)
    {
        $this->admin = $admin;
        $this->project = $project;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Project ' . $this->project->name . ' Invitation';
        $view = 'emails.project.invitation';
        return $this->subject($subject)->view($view);
    }
}
