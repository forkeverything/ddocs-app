<?php

namespace App\Http\Controllers;

use App\Factories\ChecklistFactory;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;

class InboundMailController extends Controller
{

    public function handle(Request $request)
    {
        switch ($request["OriginalRecipient"]) {
            case env('MAIL_CREATE_CHECKLIST_ADDRESS'):
                return $this->newChecklistFromEmailWebhook($request);
            default:
                break;
        }
    }

    /**
     * POST Email Webhook (via Postmark) to create a Checklist
     * only using Email cc: address.
     *
     * @param Request $request
     * @return string
     */
    public function newChecklistFromEmailWebhook(Request $request)
    {
        // If the user doesn't have an account yet
        if (!$user = User::where('email', $request["From"])->first()) {
            // we'll make one for him
            $user = User::makeNewUserFromEmailWebhook($request);
        }

        ChecklistFactory::makeFromEmail($request, $user);

        return "Received Create Checklist via Email Request";
    }

}
