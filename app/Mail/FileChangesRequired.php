<?php

namespace App\Mail;

use App\FileRequest;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FileChangesRequired extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The FileRequest.
     *
     * @var FileRequest
     */
    public $fileRequest;

    /**
     * The User that made the Checklist that holds the FileRequest.
     *
     * @var User
     */
    public $maker;

    /**
     * Create a new message instance.
     *
     * @param FileRequest $fileRequest
     */
    public function __construct(FileRequest $fileRequest)
    {
        $this->fileRequest = $fileRequest;
        $this->maker = $fileRequest->checklist->user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Changes Required: ' . $this->fileRequest->name;
        $view = 'emails.files.changes';
        return $this->subject($subject)
                    ->view($view);
    }
}
