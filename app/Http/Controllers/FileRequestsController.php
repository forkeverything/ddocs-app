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
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

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
        $fileRequest = FileRequest::findOrFail(unhashId('file-request', $fileRequestHash));
        // If user is logged in - make sure they are the recipient
//        if(Auth::check()) $this->authorize('upload', $file);

        // Only accept the File if we're waiting on one
        if ($fileRequest->hasStatus('received')) abort(409, "File already received");

        $fileRequest = UploadFactory::store($fileRequest, $request->file('file'));


        // Check if the Checklist the FileRequest belongs to is complete
        dispatch(new CheckIfChecklistComplete($fileRequest->checklist));

        // Re-load a fresh FileRequest without the checklist baggage.
        return $fileRequest->fresh();

    }

    /**
     * POST request to mark a File (File Request's most recent upload) as rejected.
     *
     * @param $fileRequestHash
     * @param RejectFileRequest $request
     * @return FileRequest
     */
    public function postRejectUploadedFile($fileRequestHash, RejectFileRequest $request)
    {
        $fileRequest = FileRequest::findOrFail(unhashId('file-request', $fileRequestHash))
                                  ->reject($request->reason);
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
        $fileRequest = FileRequest::findOrFail(unhashId('file-request', $fileRequestHash))->load('checklist', 'uploads');
        return view('file.history', compact('fileRequest'));
    }

    /**
     * Get notes for file request.
     *
     * @param $fileRequestHash
     * @return mixed
     */
    public function getNotes($fileRequestHash)
    {
        $fileRequest = FileRequest::findOrFail(unhashId('file-request', $fileRequestHash))->load('checklist', 'uploads');
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
        $fileRequest = FileRequest::findOrFail(unhashId('file-request', $fileRequestHash));
        if(! Auth::user()->can('update', $fileRequest)) return response("File Request does not belong to user.", 403);
        $fileRequest->update($request->all());
        return $fileRequest;
    }

    /**
     * Delete FileRequest
     *
     * @param $fileRequestHash
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteFiles($fileRequestHash)
    {
        $fileRequest = FileRequest::findOrFail(unhashId('file-request', $fileRequestHash));
        $uploadPaths = $fileRequest->uploads->pluck('path')->toArray();
        if(Storage::delete($uploadPaths)) $fileRequest->delete();
        return $fileRequest;
    }

    /**
     * Add a comment to a File Request.
     *
     * @param $fileRequestHash
     * @param Requests\AddCommentRequest $request
     * @return mixed
     */
    public function postAddComment($fileRequestHash, Requests\AddCommentRequest $request)
    {
        // TODO ::: Currently, anybody who is logged-in can post comments to a file request. Need to change this
        // when we implement protected checklists.

        $fileRequest = FileRequest::findOrFail(unhashId('file-request', $fileRequestHash));
        return $fileRequest->comments()->create([
            'subject_id' => $fileRequest->id,
            'subject_type' => 'App\\FileRequest',
            'body' => $request->body,
            'user_id' => Auth::id()
        ])->load('sender');
    }

}
