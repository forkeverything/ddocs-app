<?php

namespace App\Console\Commands;

use App\Checklist;
use App\Mailers\ChecklistMailer;
use App\Mailers\FileRequestMailer;
use App\Mailers\UserMailer;
use App\User;
use Illuminate\Console\Command;

class SendTestEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test emails for each view.';

    /**
     * @var UserMailer
     */
    private $userMailer;
    /**
     * @var ChecklistMailer
     */
    private $checklistMailer;
    /**
     * @var FileRequestMailer
     */
    private $fileRequestMailer;

    /**
     * Mike's User model
     * @var
     */
    private $user;

    private $checklist;

    /**
     * Create a new command instance.
     *
     * @param UserMailer $userMailer
     * @param ChecklistMailer $checklistMailer
     * @param FileRequestMailer $fileRequestMailer
     */
    public function __construct(UserMailer $userMailer, ChecklistMailer $checklistMailer, FileRequestMailer $fileRequestMailer)
    {
        parent::__construct();
        $this->userMailer = $userMailer;
        $this->checklistMailer = $checklistMailer;
        $this->fileRequestMailer = $fileRequestMailer;
        $this->checklist = Checklist::first();
        $this->user = User::first();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->sendUserEmails()
             ->sendChecklistEmails()
             ->sendFileRequestEmails();
        $this->info('and done!');
    }

    protected function sendUserEmails()
    {
        $this->userMailer->sendWelcomeEmail($this->user);
        $this->userMailer->sendNotEnoughCreditsToMakeListEmail($this->user);

        $this->info('Finished User Emails');
        return $this;
    }

    protected function sendChecklistEmails()
    {
        $this->checklistMailer->sendNewChecklistNotificationEmail($this->checklist);
        $this->checklistMailer->sendChecklistCompleteEmail($this->checklist);
        $this->checklistMailer->sendUpcomingFilesReminder($this->checklist);
        $this->checklistMailer->sendLateFilesReminder($this->checklist);
        $this->checklistMailer->sendFreeCreditsReceived($this->checklist, $this->user);

        $this->info('Finished Checklist Emails');
        return $this;
    }

    protected function sendFileRequestEmails()
    {
        $fileRequest = $this->checklist->requestedFiles->first();
        $fileRequest->uploads()->create([
            'path' => 'foo/bar.jpg',
            'rejected' => 1,
            'rejected_reason' => 'Not good enough'
        ]);
        $this->fileRequestMailer->sendChangesRequiredEmail($fileRequest);
        $this->info('Finished File Request Emails');
        return $this;
    }


}
