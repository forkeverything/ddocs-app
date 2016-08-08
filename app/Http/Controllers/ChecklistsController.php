<?php

namespace App\Http\Controllers;

use App\Checklist;
use App\Factories\ChecklistFactory;
use App\Factories\FileFactory;
use App\File;
use App\FileRequest;
use App\Http\Requests\NewChecklistRequest;
use App\Http\Requests\RejectFileRequest;
use App\Http\Requests\UploadFileRequest;
use App\Repositories\FilesRequestsRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
            'only' => ['getMakeForm', 'postNewChecklist']
        ]);
        $this->hashids = $hashidsManager;
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
