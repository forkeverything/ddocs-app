<?php

namespace App\Policies;

use App\FileRequest;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FileRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Only the owner of the Checklist that contains the FileRequest can make
     * updates to the FileRequest
     *
     * @param User $user
     * @param FileRequest $fileRequest
     * @return bool
     */
    public function update(User $user, FileRequest $fileRequest)
    {
        return $user->id === $fileRequest->checklist->user_id;
    }
}
