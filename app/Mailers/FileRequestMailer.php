<?php


namespace App\Mailers;


use App\FileRequest;

class FileRequestMailer extends Mailer
{
    /**
     *  Let the recipient know they need to make changes to a File
     *
     * @param FileRequest $fileRequest
     */
    public function sendChangesRequiredEmail(FileRequest $fileRequest)
    {
        $maker = $fileRequest->checklist->user;
        $subject = 'Files Collector - Changes Request By ' . $maker->name;
        $view = 'emails.files.changes';
        $this->sendTo($fileRequest->checklist->recipient, "File Holder", $subject, $view, compact('fileRequest', 'maker'));
    }
}