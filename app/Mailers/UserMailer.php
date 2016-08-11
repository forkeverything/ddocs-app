<?php
namespace App\Mailers;


use App\Checklist;
use App\Company;
use App\FileRequest;
use App\Project;
use App\PurchaseOrder;
use App\PurchaseRequest;
use App\User;
use Carbon\Carbon;

class UserMailer extends Mailer
{

    /**
     * Send Welcome email to users when they first sign up with us.
     *
     * @param User $user
     */
    public function sendWelcomeEmail(User $user)
    {
        $subject = 'Files Collector - Welcome to the team!';
        $view = 'emails.user.welcome';
        $this->sendTo($user->email, $user->name, $subject, $view, compact('user'));
    }

}