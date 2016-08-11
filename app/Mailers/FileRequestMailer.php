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
        $subject = 'Files Collector - ' . $maker->name . ' Needs Changes Made To ' . $fileRequest->name;
        $view = 'emails.files.changes';
        $this->sendTo($fileRequest->checklist->recipient, "Giver of Files", $subject, $view, compact('fileRequest', 'maker'));
    }
}