<?php

namespace App\Policies;

use App\Checklist;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChecklistPolicy
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
     * Determine if authenticated User can give given Checklist.
     *
     * @param User $user
     * @param Checklist $checklist
     * @return bool
     */
    public function view(User $user, Checklist $checklist)
    {
        if($user->id === $checklist->user_id) return true;
        return strcasecmp($user->email, $checklist->recipient) === 0;
    }
}
