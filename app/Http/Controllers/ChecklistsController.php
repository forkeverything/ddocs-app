<?php

namespace App\Http\Controllers;

use App\Checklist;
use App\Factories\ChecklistFactory;
use App\Factories\FileFactory;
use App\File;
use App\Http\Requests\NewChecklistRequest;
use App\Http\Requests\UploadFileRequest;
use App\Repositories\ChecklistsRepository;
use App\Repositories\FilesRepository;
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

    public function getSingleChecklist(Request $request, $checklistHash)
    {
        $checklist = Checklist::findOrFail(unhashId($checklistHash));
//        if(Auth::check()) $this->authorize('view', $checklist);
        return view('checklist.single', compact('checklist', 'checklistHash'));
    }

    public function getFilesForChecklist(Request $request, $checklistHash)
    {
        $checklist = Checklist::findOrFail(unhashId($checklistHash));
        $sort = $request->sort;
        $order = $request->order;
        $search = $request->search;
        $perPage = $request->per_page ?: 20;
        return FilesRepository::forChecklist($checklist)
                                ->searchFor($search)
                                ->sortOn($sort, $order)
                                ->paginate($perPage);
    }

    public function postUploadFile($checklistHash, File $file, UploadFileRequest $request)
    {
        // If user is logged in - make sure they are the recipient
//        if(Auth::check()) $this->authorize('upload', $file);

        // Only accept the File if we're waiting on one
        if(! $file->hasStatus('waiting')) abort(409, "File already received");

        return FileFactory::store($file, $request->file('file'));

    }
}
