<?php

namespace App\Http\Controllers;

use App\Checklist;
use App\Factories\ChecklistFactory;
use App\Http\Requests\NewChecklistRequest;
use App\Repositories\ChecklistsRespository;
use App\Repositories\FilesRequestsRepository;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\HashidsManager;

class ChecklistsController extends Controller
{
    /**
     * HashId class to encode/decode ID numbers
     * @var HashidsManager
     */
    protected $hashids;

    /**
     * ChecklistsController constructor.
     *
     * @param HashidsManager $hashidsManager
     */
    public function __construct(HashidsManager $hashidsManager)
    {
        $this->middleware('auth', [
            'only' => [
                'getListsView',
                'getForAuthenticatedUser',
                'getMakeForm',
                'postNewChecklist'
            ]
        ]);
        $this->hashids = $hashidsManager;
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
        return $this->hashids->encode($checklist->id);
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
        if (! $user = User::where('email', $request["From"])->first()){
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
    public function getSingleChecklist($checklistHash)
    {
        $checklist = Checklist::findOrFail(unhashId($checklistHash));
        $checklist->load('recipients');

//        if(Auth::check()) $this->authorize('view', $checklist);
        return view('checklist.single', compact('checklist', 'checklistHash'));
    }


    /**
     * Retrieve the files for checklist.
     *
     * @param Request $request
     * @param $checklistHash
     * @return FilesRequestsRepository
     */
    public function getFilesForChecklist(Request $request, $checklistHash)
    {
        $checklist = Checklist::findOrFail(unhashId($checklistHash));
        $sort = $request->sort;
        $order = $request->order;
        $search = $request->search;
        $perPage = $request->per_page ?: 20;
        return FilesRequestsRepository::forChecklist($checklist)
                                      ->filterIntegerField('version', $request->version)
                                      ->filterDateField('due', $request->due)
                                      ->withStatus($request->status)
                                      ->searchFor($search)
                                      ->sortOn($sort, $order)
                                      ->withNumReceivedFiles()
                                      ->paginate($perPage);
    }


}
