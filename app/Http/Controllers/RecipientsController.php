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
    public function getTurnOffNotifications($recipientHash)
    {
        if($recipient = Recipient::findByHash($recipientHash)) $recipient->turnOffNotifications();
        return view('recipients.turned-off-notifications', compact('recipient'));
    }
}
