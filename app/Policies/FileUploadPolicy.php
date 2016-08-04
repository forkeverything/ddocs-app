<?php

namespace App\Policies;

use App\Checklist;
use App\File;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FileUploadPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function upload(User $user, File $file)
    {
        return $user->email === $file->checklist->recipient;
    }

}
