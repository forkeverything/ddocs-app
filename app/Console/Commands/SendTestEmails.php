<?php

namespace App\Console\Commands;

use App\Checklist;
use App\Jobs\DeleteEmailTestUser;
use App\Mail\ChecklistComplete;
use App\Mail\FileChangesRequired;
use App\Mail\FreeCreditsReceived;
use App\Mail\LateFilesReminder;
use App\Mail\NewChecklist;
use App\Mail\NotEnoughCreditsForList;
use App\Mail\UpcomingFilesReminder;
use App\Mail\Welcome;
use App\Mail\WelcomeWithGeneratedPassword;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class SendTestEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:test {email=mail@wumike.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test emails for each view.';

    /**
     * Mike's User model
     * @var
     */
    private $user;

    /**
     * The first Checklist in DB We assume it's the dev seeded one.
     * TODO ::: Create a new checklist so it'll always be consistent.
     * @var
     */
    private $checklist;

    /**
     * The User model that has the same email we're sending emaisl too.
     *
     * @var
     */
    private $existingUser;

    /*
    *
     * @var int
     */
    protected $sentEmails = 0;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {


        if(! Schema::hasTable('checklists')) return;

        $this->existingUser = User::where('email', $this->argument('email'))->first();

        if($this->existingUser) $this->existingUser->update(['email' => 'temporaryemail@emailz.com']);

        $this->user = User::create([
            'name' => 'John Dough',
            'email' => $this->argument('email'),
            'password' => bcrypt('secret')
        ]);


        $this->checklist = Checklist::create([
            'recipient' => $this->argument('email'),
            'name' => 'Test Checklist',
            'description' => 'Best checklist that could.',
            'user_id' => $this->user->id
        ]);




        $this->sendUserEmails()
             ->sendChecklistEmails()
             ->sendFileRequestEmails()
        ->cleanUpRecords();
        $this->info('and done! Sent Emails = ' . $this->sentEmails);
    }

    protected function sendUserEmails()
    {
        Mail::to($this->user)->send(new Welcome($this->user));
        $this->sentEmails ++;
        Mail::to($this->user)->send(new WelcomeWithGeneratedPassword($this->user, 'abcd1234'));
        $this->sentEmails ++;
        Mail::to($this->user)->send(new NotEnoughCreditsForList($this->user));
        $this->sentEmails ++;

        $this->info('Finished User Emails');
        return $this;
    }

    protected function sendChecklistEmails()
    {

        Mail::to($this->checklist->recipient)->send(new NewChecklist($this->checklist));
        $this->sentEmails ++;

        Mail::to($this->checklist->user)->send(new ChecklistComplete($this->checklist));
        $this->sentEmails ++;

        Mail::to($this->checklist->recipient)->send(new UpcomingFilesReminder($this->checklist));
        $this->sentEmails ++;

        Mail::to($this->checklist->recipient)->send(new LateFilesReminder($this->checklist));
        $this->sentEmails ++;


        Mail::to($this->checklist->user)->send(new FreeCreditsReceived($this->checklist, $this->user));
        $this->sentEmails ++;

        $this->info('Finished Checklist Emails');
        return $this;
    }

    protected function sendFileRequestEmails()
    {

        $fileRequest =  $this->checklist->requestedFiles()->create([
            'name' => 'Super important file',
            'description' => 'not much to say here',
            'due' => '01/06/2017',
            'required' => 1,
            'checklist_id' => $this->checklist->id
        ]);

        $fileRequest->uploads()->create([
            'path' => 'foo/bar.jpg',
            'rejected' => 1,
            'rejected_reason' => 'Not good enough'
        ]);


        Mail::to($this->checklist->recipient)->send(new FileChangesRequired($fileRequest));

        $this->sentEmails ++;
        $this->info('Finished File Request Emails');
        return $this;
    }

    protected function cleanUpRecords()
    {
        // Dispatch a job onto the queue so we don't delete prematurely.
        dispatch(new DeleteEmailTestUser($this->user, $this->existingUser, $this->argument('email')));
        return $this;
    }

}
