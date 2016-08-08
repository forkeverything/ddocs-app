<?php

namespace App\Http\Controllers;

use App\Factories\FileFactory;
use App\FileRequest;
use App\Http\Requests\RejectFileRequest;
use App\Http\Requests\UploadFileRequest;
use Illuminate\Http\Request;

use App\Http\Requests;

class FilesController extends Controller
{

    /**
     * Handle POST request to upload a file for a File Request within a Checklist.
     *
     * @param FileRequest $fileRequest
     * @param UploadFileRequest $request
     * @return mixed
     */
    public function postUploadFile(FileRequest $fileRequest, UploadFileRequest $request)
    {
        // If user is logged in - make sure they are the recipient
//        if(Auth::check()) $this->authorize('upload', $file);

        // Only accept the File if we're waiting on one
        if (! $fileRequest->hasStatus('waiting')) abort(409, "File already received");

        return FileFactory::store($fileRequest, $request->file('file'));

    }

    public function postRejectUploadedFile(FileRequest $fileRequest, RejectFileRequest $request)
    {
        return $fileRequest->reject($request->reason);
    }

}
