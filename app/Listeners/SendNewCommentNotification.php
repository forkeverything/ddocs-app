<?php

namespace App\Listeners;

use App\Events\CommentAdded;
use App\Notifications\NewCommentNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class SendNewCommentNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CommentAdded $event
     * @return void
     */
    public function handle(CommentAdded $event)
    {
        $comment = $event->comment;
        $subject = $comment->subject;
        $subjectClass = class_basename($subject);
        $notifyTargets = [];

        if ($subjectClass === "FileRequest") $notifyTargets = $subject->checklist->recipients;
        if ($subjectClass === "ProjectFile") $notifyTargets = $subject->members;

        $notifyTargets->reject(function ($value, $key) use ($comment) {
            return $value->email === $comment->sender->email;
        });

        foreach ($notifyTargets as $target) {
            $target->notify(new NewCommentNotification($comment));
        }
    }
}
