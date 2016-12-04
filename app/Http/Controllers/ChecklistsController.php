<?php

namespace App\Http\Controllers;

use App\Checklist;
use App\Factories\ChecklistFactory;
use App\Http\Requests\NewChecklistRequest;
use App\Repositories\ChecklistsRespository;
use App\Repositories\FileRequestsRepository;
use App\User;
use DB;
use Hash;
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
                                    ->searchWithRecipients($search)
                                    ->sortOn($sort, $order)
                                    ->paginate($perPage);
    }

    /**
     * Return the Authenticated User's checklists as JSON from
     * the repository.
     *
     * @param Request $request
     * @return ChecklistsRespository
     */
    public function getRecent(Request $request)
    {
        return ChecklistsRespository::forUser(Auth::user())
                                    ->take(5)
                                    ->sortOn('created_at', 'desc')
                                    ->getWithoutQueryProperties();

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
     * Attempt to view Checklist
     *
     * @param Checklist $checklist
     * @param $password
     * @return bool
     * @internal param $checklistHash
     */
    protected function attempt(Checklist $checklist , $password)
    {
        if (! $checklist->password) return true;
        if ($user = Auth::user()) {
            $isRecipient = !! $checklist->recipients()->where('email', $user->email)->select(DB::Raw(1))->first();
            if ($checklist->user_id === Auth::id() || $isRecipient) return true;
        }
        if(Hash::check($password, $checklist->password)) return true;
        return false;
    }

    /**
     * Checklist at given Hash
     *
     * @param $checklistHash
     * @param Request $request
     * @return Model
     */
    public function getSingle($checklistHash, Request $request)
    {
        $checklist = Checklist::findByHash($checklistHash)->load('user', 'recipients');
        if($this->attempt($checklist, $request->password)) return $checklist;
        if($request->password) return response()->json("Wrong password", 422);
        return response("Not authorized to view checklist.", 403);
    }

    /**
     * Retrieve the files for Open Checklist.
     *
     * @param Request $request
     * @param $checklistHash
     * @return FileRequestsRepository
     */
    public function getFilesOpenChecklist(Request $request, $checklistHash)
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
     * Retrieve the files for Secure Checklist.
     *
     * @param Request $request
     * @param $checklistHash
     * @return FileRequestsRepository
     */
    public function getFilesSecureChecklist(Request $request, $checklistHash)
    {
        $checklist = Checklist::findByHash($checklistHash);
        if($this->attempt($checklist, $request->password)) return $this->getFilesOpenChecklist($request, $checklistHash);
        return response("Not authorized to view files for that checklist", 403);
    }

    /**
     * Request to update checklist.
     *
     * @param Request $request
     * @param $checklistHash
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function putUpdate(Request $request, $checklistHash)
    {
        $checklist = Checklist::findByHash($checklistHash);
        $this->authorize('update', $checklist);
        if ($checklist->update($request->all())) return response('Updated checklist.');
        return response("Couldn't update checklist", 500);
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

    /**
     * Set a new password for Checklist.
     *
     * @param Request $request
     * @param $checklistHash
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function postNewPassword(Request $request, $checklistHash)
    {
        $checklist = Checklist::findByHash($checklistHash);
        $this->authorize('update', $checklist);
        $newPassword = $request->new_password ? Hash::make($request->new_password) : null;
        $updated = $checklist->update([
            'password' => $newPassword
        ]);
        if($updated) return response("Updated checklist password");
        return response("Error changing checklist password", 500);
    }

    /**
     * @param $checklistHash
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response|void
     */
    public function delete($checklistHash)
    {
        $checklist = Checklist::findByHash($checklistHash);
        $this->authorize('update', $checklist);
        if ($checklist->fullDelete()) return response("Deleted checklist");
        return abort(500, "Error deleting checklist.");
    }
}
