<?php

namespace App\Policies;

use App\File;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FileUploadPolicy
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
     * Only Logged-in User who is recipient may upload.
     *
     * @param User $user
     * @param File $file
     * @return bool
     */
    public function upload(User $user, File $file)
    {
        return $user->email === $file->checklist->recipient;
    }
}
