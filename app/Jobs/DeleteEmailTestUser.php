<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteEmailTestUser implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $dummyUser;
    /**
     * @var User
     */
    private $existingUser;
    /**
     * @var
     */
    private $email;

    /**
     * Create a new job instance.
     *
     * @param User $dummyUser
     * @param User $existingUser
     * @param $email
     */
    public function __construct(User $dummyUser, User $existingUser, $email)
    {
        $this->dummyUser = $dummyUser;
        $this->existingUser = $existingUser;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->dummyUser->delete();
        if($this->existingUser) $this->existingUser->update(['email' => $this->email]);
    }
}
