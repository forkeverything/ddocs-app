<?php

namespace App\Http\Controllers;

use App\Recipient;
use Illuminate\Http\Request;

use App\Http\Requests;

class RecipientsController extends Controller
{
    /**
     * Turn off notifications for a given Recipient.
     *
     * @param $recipientHash
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function postTurnOffNotifications($recipientHash)
    {
        if($recipient = Recipient::findByHash($recipientHash))  {
            $recipient->turnOffNotifications();
            return response("Turned off notifications");
        }
        return response("Couldn't find recipient", 400);
    }
}
