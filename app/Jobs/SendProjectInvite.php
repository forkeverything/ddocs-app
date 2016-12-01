<?php

namespace App\Jobs;

use App\Project;
use App\User;
use App\Utilities\CSTransactionalEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendProjectInvite implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var Project
     */
    private $project;
    /**
     * @var User
     */
    private $inviter;
    /**
     * @var
     */
    private $recipientEmail;

    /**
     * Create a new job instance.
     *
     * @param Project $project
     * @param $recipientEmail
     * @param User $inviter
     */
    public function __construct(Project $project, $recipientEmail, User $inviter)
    {
        $this->project = $project;
        $this->inviter = $inviter;
        $this->recipientEmail = $recipientEmail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $acceptLink = env('APP_URL') . '/projects/' . $this->project->id . '/join';
        CSTransactionalEmail::send("b39041af-14b3-4f49-8045-560fcdb098e2", $this->recipientEmail, null, [
            "project_name" => $this->project->name,
            "inviter_name" => $this->inviter->name,
            "inviter_email" => $this->inviter->email,
            "accept_link" => $acceptLink,
        ]);

    }
}
