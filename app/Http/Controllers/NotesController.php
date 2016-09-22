<?php

namespace App\Http\Controllers;

use App\FileRequest;
use App\Http\Requests\AddNoteRequest;
use App\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{

    /**
     * Post a new Note.
     *
     * @param AddNoteRequest $request
     * @return Note|void
     */
    public function postNew(AddNoteRequest $request)
    {
        $fileRequest = FileRequest::findOrFail(unhashId('file-request', $request->file_request_hash));
        return $fileRequest->notes()->create([
            'body' => $request->body,
            'position' => $request->position
        ]);
    }

    /**
     * Make updates to a Note, including changing it's body or marking as checked.
     *
     * @param Request $request
     * @param $noteHash
     * @return Note|void
     * @internal param Note $note
     */
    public function putUpdate(Request $request, $noteHash)
    {
        $note = Note::findOrFail(unhashId('note', $noteHash));
        $note->update($request->all());
        return $note;
    }

    /**
     * Delete a note from records.
     *
     * @param $noteHash
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response|void
     * @internal param Note $note
     */
    public function delete($noteHash)
    {
        $note = Note::findOrFail(unhashId('note', $noteHash));
        if($note->delete()) return response("Deleted note");
        return response("Couldn't delete note", 500);
    }
}
