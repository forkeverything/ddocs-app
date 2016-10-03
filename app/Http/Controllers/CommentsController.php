<?php

namespace App\Http\Controllers;

use App\FileRequest;
use App\ProjectFile;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', [
            'only' => [
                'getForProjectFile'
            ]
        ]);
    }

    /**
     * Get all the commentse for given Project File.
     *
     * @param ProjectFile $projectFile
     * @return mixed
     */
    public function getForProjectFile(ProjectFile $projectFile)
    {
        if(! Auth::user()->can('view', $projectFile->folder->project)) abort("Not authorized to view that project file");
        return $projectFile->comments;
    }

    /**
     * Get comments for File Request.
     *
     * @param $fileRequestHash
     * @return mixed
     */
    public function getForFileRequest($fileRequestHash)
    {
        $fileRequest = FileRequest::findOrFail(unhashId('file-request', $fileRequestHash));
        return $fileRequest->comments;
    }

}
