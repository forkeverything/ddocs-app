<?php


namespace App\Utilities\Traits;


use App\Recipient;
use App\User;

trait SendsRecipientNotifications
{
    /**
     * Tries to link Recipient to User via email.
     *
     * @param Recipient $recipient;
     */
    public function attemptLinkRecipientToUser(Recipient $recipient)
    {
        if($recipient->user_id) return;
        if($user = User::findByEmail($recipient->email)) {
            $recipient->user_id = $user->id;
            $recipient->save();
        }
    }
}