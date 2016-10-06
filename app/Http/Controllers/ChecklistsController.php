<?php

namespace App\Http\Controllers;

use App\Checklist;
use App\Factories\ChecklistFactory;
use App\Http\Requests\NewChecklistRequest;
use App\Repositories\ChecklistsRespository;
use App\Repositories\FileRequestsRepository;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChecklistsController extends Controller
{

    /**
     * ChecklistsController constructor.
     *
     */
    public function __construct()
    {
        $this->middleware('auth', [
            'only' => [
                'getListsView',
                'getMakeForm',
                'getAllRequestsForUser'
            ]
        ]);
    }

    /**
     * Show the view for viewing all Checklist(s) made by User.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getListsView()
    {
        return view('checklist.all');
    }

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
     * Return the view to make a new Checklist.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getMakeForm()
    {
        return view('checklist.make');
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
     * Get the view for a single Checklist.
     *
     * @param $checklistHash
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSingle($checklistHash)
    {
        $checklist = Checklist::findOrFail(unhashId('checklist', $checklistHash));
        $checklist->load('user', 'recipients');

        return $checklist;
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
        $checklist = Checklist::findOrFail(unhashId('checklist', $checklistHash));
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
     * Find FileRequest(s) that belongs to any Checklist that was created by the
     * authenticated User.
     *
     * @param User $user
     * @param Request $request
     */
    public function getFileRequestsForUser(User $user, Request $request)
    {
        if(! Auth::id() === $user->id) abort(403, "Trying to get file requests for user that is not authenticated");
        $searchTerm = $request->search;
        FileRequestsRepository::forUser($user)
            ->searchChecklistNames($searchTerm)
            ->searchRecipientEmails($searchTerm)
            ->searchFor($searchTerm)            // Searches get performed in reverse of method call - so our original WHERE has to come first
            ->with('checklist.recipients')      // Include checklist as well as list of recipients.
            ->getWithoutQueryProperties();
    }


}
