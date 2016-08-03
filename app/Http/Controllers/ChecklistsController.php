<?php

namespace App\Http\Controllers;

use App\Checklist;
use App\Factories\ChecklistFactory;
use App\Http\Requests\NewChecklistRequest;
use App\Repositories\ChecklistsRepository;
use App\Repositories\FilesRepository;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
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
        $this->middleware('auth');
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
        $id = array_first($this->hashids->decode($checklistHash));
        $checklist = Checklist::findOrFail($id);
        $sort = $request->sort;
        $order = $request->order;
        $search = $request->search;
        $perPage = $request->per_page ?: 20;
        $files = FilesRepository::forChecklist($checklist)
                                ->searchFor($search)
                                ->sortOn($sort, $order)
            ->paginate($perPage);
        return view('checklist.single', compact('checklist', 'files', 'sort', 'order', 'search', 'perPage'));
    }
}
