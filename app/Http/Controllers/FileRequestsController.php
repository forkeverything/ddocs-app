<?php

namespace App\Http\Controllers;

use App\Events\FileWasRejected;
use App\Events\FileWasUploaded;
use App\Factories\UploadFactory;
use App\File;
use App\FileRequest;
use App\Http\Requests\RejectFileRequest;
use App\Http\Requests\UploadFileRequest;
use App\Jobs\CheckIfChecklistComplete;
use App\Repositories\FileRequestsRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use Event;
use Storage;


class FileRequestsController extends Controller
{

    /**
     * Handle POST request to upload a file for a File Request within a Checklist.
     *
     * @param $fileRequestHash
     * @param UploadFileRequest $request
     * @return mixed
     */
    public function postUploadFile($fileRequestHash, UploadFileRequest $request)
    {
        $fileRequest = FileRequest::findByHash($fileRequestHash);
        // If user is logged in - make sure they are the recipient
//        if(Auth::check()) $this->authorize('upload', $file);

        // Only accept the File if we're waiting on one
        if ($fileRequest->hasStatus('received')) abort(409, "File already received");

        UploadFactory::store($fileRequest, $request->file('file'));

        $fileRequest->update([
            'status' => 'received'
        ]);

        // Check if the Checklist the FileRequest belongs to is complete
        dispatch(new CheckIfChecklistComplete($fileRequest->checklist));

        // Re-load a fresh FileRequest without the checklist baggage attached from event.
        return $fileRequest->fresh();

    }

    /**
     * POST request to mark a File (File Request's most recent upload) as rejected.
     *
     * @param $fileRequestHash
     * @param RejectFileRequest $request
     * @return Model
     */
    public function postRejectUploadedFile($fileRequestHash, RejectFileRequest $request)
    {
        $fileRequest = FileRequest::findByHash($fileRequestHash)
                                  ->reject($request->reason);
        $this->authorize('update', $fileRequest);
        Event::fire(new FileWasRejected($fileRequest));
        return $fileRequest;
    }

    /**
     * The history view with different versions of a File Request.
     *
     * @param $fileRequestHash
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getHistory($fileRequestHash)
    {
        $fileRequest = FileRequest::findByHash($fileRequestHash)->load('checklist', 'uploads');
        return view('file.history', compact('fileRequest'));
    }

    /**
     * Get notes for file request.
     *
     * @param $fileRequestHash
     *
     * @return mixed
     */
    public function getNotes($fileRequestHash)
    {
        $fileRequest = FileRequest::findByHash($fileRequestHash)->load('checklist', 'uploads');
        return $fileRequest->notes()->orderBy('position', 'ASC')->get();
    }

    /**
     * Modify FileRequest
     *
     * @param $fileRequestHash
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function putModifyRequest($fileRequestHash, Request $request)
    {
        $fileRequest = FileRequest::findByHash($fileRequestHash);
        $this->authorize('update', $fileRequest);
        $fileRequest->update($request->all());
        return $fileRequest;
    }

    /**
     * Delete FileRequest
     *
     * @param $fileRequestHash
     * @return Model
     */
    public function deleteFiles($fileRequestHash)
    {
        $fileRequest = FileRequest::findByHash($fileRequestHash);
        $this->authorize('update', $fileRequest);
        $uploadPaths = $fileRequest->uploads->pluck('path')->toArray();
        if (Storage::delete($uploadPaths)) $fileRequest->delete();
        return $fileRequest;
    }

    /**
     * Find FileRequest(s) that belong to a Checklist
     * made by the Authenticated User.
     *
     * @param Request $request
     * @return mixed
     */
    public function getForUser(Request $request)
    {
        $search = $request->search;
        return FileRequestsRepository::forUser(Auth::user())
                                     ->searchFor($search)
                                     ->searchChecklistNamesAndRecipientEmails($search)
                                     ->with('checklist')
                                     ->paginate(6);
    }


}
