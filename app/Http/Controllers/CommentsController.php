<?php

namespace App\Http\Controllers;

use App\Comment;
use App\FileRequest;
use App\Http\Requests\AddCommentRequest;
use App\ProjectFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentsController extends Controller
{

    /**
     * Get all the comments for given Project File.
     *
     * @param ProjectFile $projectFile
     * @return mixed
     */
    public function getProjectFile(ProjectFile $projectFile)
    {
        $this->authorize('view', $projectFile->folder->project);
        return $projectFile->comments;
    }

    /**
     * Get comments for File Request.
     *
     * @param $fileRequestHash
     * @return mixed
     */
    public function getFileRequest($fileRequestHash)
    {
        $fileRequest = FileRequest::findOrFail(unhashId('file-request', $fileRequestHash));
        return $fileRequest->comments;
    }

    /**
     * Add Comment to a ProjectFile.
     *
     * @param ProjectFile $projectFile
     * @param AddCommentRequest $request
     * @return Model
     */
    public function postNewProjectFile(ProjectFile $projectFile, AddCommentRequest $request)
    {
        $this->authorize('updateFile', [$projectFile->folder->project, $projectFile]);
        return Comment::addComment($projectFile->id, 'App\\ProjectFile', $request->body, Auth::id())
                           ->load('sender');
    }

    /**
     * Add a comment to a File Request.
     *
     * @param $fileRequestHash
     * @param Requests\AddCommentRequest $request
     * @return mixed
     */
    public function postNewFileRequest($fileRequestHash, AddCommentRequest $request)
    {
        // TODO ::: Currently, anybody who is logged-in can post comments to a file request. Need to change this
        // when we implement protected checklists.

        $fileRequest = FileRequest::findOrFail(unhashId('file-request', $fileRequestHash));

        return Comment::addComment($fileRequest->id, 'App\\FileRequest', $request->body, Auth::id())
                           ->load('sender');
    }


}
