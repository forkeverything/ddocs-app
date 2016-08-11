<?php


namespace App\Mailers;


use App\Checklist;
use Carbon\Carbon;

class ChecklistMailer extends Mailer
{
    /**
     * Send a notification to recipient when a checklist has been made for
     * them.
     *
     * @param Checklist $checklist
     */
    public function sendNewChecklistNotificationEmail(Checklist $checklist)
    {
        $maker = $checklist->user;
        $subject = 'Files Collector - ' . $maker->name . '\'s Request For Files';
        $view = 'email.checklist.new';
        $this->sendTo($checklist->recipient, "Giver of Files", $subject, $view, compact('maker', 'checklist'));
    }

    /**
     * Email to let the recipient know that there's upcoming File(s) soon.
     *
     * @param Checklist $checklist
     */
    public function sendUpcomingFilesReminder(Checklist $checklist)
    {
        $subject = 'Files Collector - ' . 'Due Files Coming Up';
        $view = 'email.files.reminder-upcoming';
        $upcomingFiles = $checklist->requestedFiles()
                                    ->whereDate('due', '=', Carbon::now()->addDays(4)->format('Y-m-d'))
                                   ->get();
        $this->sendTo($checklist->recipient, "Giver of Files", $subject, $view, compact('checklist', 'upcomingFiles'));
    }

    /**
     * Email to let the recipient know that there are late File(s) needed from them.
     *
     * @param Checklist $checklist
     */
    public function sendLateFilesReminder(Checklist $checklist)
    {
        $today = Carbon::now();
        $subject = 'Files Collector - ' . 'Overdue Files Reminder';
        $view = 'email.files.reminder-late';
        $lateFiles = $checklist->requestedFiles()
                               ->whereDate('due', '<', $today)->format('Y-m-d')
                               ->get();
        $this->sendTo($checklist->recipient, "Giver of Files", $subject, $view, compact('checklist', 'lateFiles', 'today'));
    }

    /**
     * Email to send to the maker of the Checklist to let him/her know
     * that all the files have been collected.
     *
     * @param Checklist $checklist
     */
    public function sendChecklistCompleteEmail(Checklist $checklist)
    {
        $maker = $checklist->user;
        $subject = 'Files Collector - Another Completed Checklist: ' . $checklist->name;
        $view = 'email.checklist.complete';
        $this->sendTo($maker->email, $maker->name, $subject, $view, compact('checkllist', 'maker'));
    }

}