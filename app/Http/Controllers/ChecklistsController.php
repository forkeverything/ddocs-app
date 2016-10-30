<?php

namespace App\Http\Controllers;

use App\Checklist;
use App\Factories\ChecklistFactory;
use App\Http\Requests\NewChecklistRequest;
use App\Repositories\ChecklistsRespository;
use App\Repositories\FileRequestsRepository;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChecklistsController extends Controller
{

    /**
     * Return the Authenticated User's checklists as JSON from
     * the repository.
     *
     * @param Request $request
     * @return ChecklistsRespository
     */
    public function getForAuthenticatedUser(Request $request)
    {
        $sort = $request->sort;
        $order = $request->order;
        $search = $request->search;
        $perPage = $request->per_page ?: 20;

        return ChecklistsRespository::forUser(Auth::user())
                                    ->searchFor($search)
                                    ->sortOn($sort, $order)
                                    ->paginate($perPage);
    }

    /**
     * Handle POST request to make a new Checklist.
     *
     * @param NewChecklistRequest $request
     * @return mixed
     */
    public function postNewChecklist(NewChecklistRequest $request)
    {
        $checklist = ChecklistFactory::make($request, Auth::user());
        return hashId('checklist', $checklist);
    }

    /**
     * POST Email Webhook (via Postmark) to create a Checklist
     * only using Email cc: address.
     *
     * @param Request $request
     * @return string
     */
    public function postNewChecklistFromEmailWebhook(Request $request)
    {
        // Is it going to the right cc address: list@in.filescollector.com
        if ($request["OriginalRecipient"] !== 'list@in.filescollector.com') return "Wrong Email Address To Create Checklist";

        // If the user doesn't have an account yet
        if (!$user = User::where('email', $request["From"])->first()) {
            // we'll make one for him
            $user = User::makeNewUserFromEmailWebhook($request);
        }

        ChecklistFactory::makeFromEmail($request, $user);

        return "Received Create Checklist via Email Request";
    }


    /**
     * Checklist at given Hash
     *
     * @param $checklistHash
     * @return Model
     */
    public function getSingle($checklistHash)
    {
        return Checklist::findByHash($checklistHash)->load('user', 'recipients');
    }

    /**
     * Retrieve the files for checklist.
     *
     * @param Request $request
     * @param $checklistHash
     * @return FileRequestsRepository
     */
    public function getFilesForChecklist(Request $request, $checklistHash)
    {
        $checklist = Checklist::findByHash($checklistHash);
        $sort = $request->sort;
        $order = $request->order;
        $search = $request->search;
        $perPage = $request->per_page ?: 20;
        return FileRequestsRepository::forChecklist($checklist)
                                     ->filterIntegerField('version', $request->version)
                                     ->filterDateField('due', $request->due)
                                     ->withStatus($request->status)
                                     ->searchFor($search)
                                     ->sortWithNull($sort, $order, ['due'])
                                     ->withNumReceivedFiles()
                                     ->paginate($perPage);
    }

    /**
     * Edit recipients for an existing Checklist.
     *
     * @param Request $request
     * @param $checklistHash
     * @return \App\Recipient[]|\Illuminate\Database\Eloquent\Collection
     */
    public function putUpdateRecipients(Request $request, $checklistHash)
    {
        $checklist = Checklist::findByHash($checklistHash);
        $this->authorize('update', $checklist);
        return ChecklistFactory::updateRecipients($checklist, $request->recipients);
    }

    /**
     * Add a new FileRequest to an existing Checklist.
     *
     * @param Request $request
     * @param $checklistHash
     * @return Model
     */
    public function postAddFile(Request $request, $checklistHash)
    {
        $checklist = Checklist::findByHash($checklistHash);
        $this->authorize('update', $checklist);
        return ChecklistFactory::addFileToExistingChecklist($checklist, $request);
    }
}
