<?php

namespace App\Http\Controllers;

use App\FileRequest;
use App\Http\Requests\AddNoteRequest;
use App\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Post a new Note.
     *
     * @param AddNoteRequest $request
     * @return Note|void
     */
    public function postNew(AddNoteRequest $request)
    {
        $fileRequest = FileRequest::findOrFail(unhashId('file-request', $request->file_request_hash));
        if($fileRequest->checklist->user_id !== Auth::id()) return response("Not allowed to add notes to that File Request.", 403);
        return $fileRequest->notes()->create([
            'body' => $request->body,
            'position' => $request->position
        ]);
    }

    /**
     * Make updates to a Note, including changing it's body or marking as checked.
     *
     * @param Request $request
     * @param Note $note
     * @return Note|void
     */
    public function putUpdate(Request $request, Note $note)
    {
        if(! Auth::user()->can('change', $note)) return response("File Request the Note is attached to does not belong to user.", 403);
        $note->update($request->all());
        return $note;
    }

    /**
     * Delete a note from records.
     *
     * @param Note $note
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response|void
     */
    public function delete(Note $note)
    {
        if(! Auth::user()->can('change', $note)) return response("File Request the Note is attached to does not belong to user.", 403);
        if($note->delete()) return response("Deleted note");
        return response("Couldn't delete note", 500);
    }
}
