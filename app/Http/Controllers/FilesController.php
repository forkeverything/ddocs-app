<?php

namespace App\Http\Controllers;

use App\Events\FileWasRejected;
use App\Events\FileWasUploaded;
use App\Factories\FileFactory;
use App\File;
use App\FileRequest;
use App\Http\Requests\RejectFileRequest;
use App\Http\Requests\UploadFileRequest;
use App\Jobs\CheckIfChecklistComplete;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Event;

class FilesController extends Controller
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

        $fileRequest = FileFactory::store($fileRequest, $request->file('file'));


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

    public function getHistory($fileRequestHash)
    {
        $fileRequest = FileRequest::findOrFail(unhashId('file-request', $fileRequestHash))->load('checklist', 'uploads');
        return view('file.history', compact('fileRequest'));
    }
}
