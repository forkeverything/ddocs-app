<?php

namespace App\Http\Controllers;

use App\Checklist;
use App\Events\ChecklistCreated;
use App\Factories\ChecklistFactory;
use App\Factories\FileFactory;
use App\File;
use App\FileRequest;
use App\Http\Requests\NewChecklistRequest;
use App\Http\Requests\RejectFileRequest;
use App\Http\Requests\UploadFileRequest;
use App\Repositories\ChecklistsRespository;
use App\Repositories\FilesRequestsRepository;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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

        return ChecklistsRespository::forUser(Auth::user())
                                    ->searchFor($search)
                                    ->sortOn($sort, $order)
                                    ->paginate(20);
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

        Event::fire(new ChecklistCreated($checklist));

        return $this->hashids->encode($checklist->id);
    }

    public function postNewChecklistFromEmailWebhook(Request $request)
    {
        $request = $request->all();

        // is it going to the right cc address: list@in.filescollector.com
        if($request["OriginalRecipient"] !== 'list@in.filescollector.com') return "Wrong Email Address To Create Checklist";

        $body = $request["TextBody"];

        preg_match_all("/^-([^\[\n]*)(\[(.*)\])?/m", $body, $matches);

        $requestedFiles = [];

        foreach ($matches[1] as $key => $fileName) {
            $file = [
                "name" => $fileName,
                "due" => isset($matches[3][$key]) ? $matches[3][$key] : null
            ];
            array_push($requestedFiles, $file);
        }

        // Build our form request manually
        $newChecklistRequest = new NewChecklistRequest([
            'recipient' => $request["ToFull"][0]["Email"],
            'name' => $request["Subject"],
            'requested_files' => $requestedFiles
        ]);

        $user = User::where('email', $request["From"])->first();

        $checklist = ChecklistFactory::make($newChecklistRequest, $user);

        Event::fire(new ChecklistCreated($checklist));

        return "Received Create Checklist via Email Request";
    }


    /**
     * Get the view for a single Checklist.
     *
     * @param Request $request
     * @param $checklistHash
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSingleChecklist(Request $request, $checklistHash)
    {
        $checklist = Checklist::findOrFail(unhashId($checklistHash));
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
                                      ->whereRequired($request->required)
                                      ->filterIntegerField('version', $request->version)
                                      ->filterDateField('due', $request->due)
                                      ->withStatus($request->status)
                                      ->searchFor($search)
                                      ->sortOn($sort, $order)
                                      ->withNumReceivedFiles()
                                      ->paginate($perPage);
    }


}
