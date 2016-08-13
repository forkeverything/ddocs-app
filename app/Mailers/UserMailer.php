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

    /**
     * Let the User know they we couldn't make a recent list they've requested
     * because they don't have enough credits.
     *
     * @param User $user
     */
    public function sendNotEnoughCreditsToMakeListEmail(User $user)
    {
        $subject = 'Files Collector - Not Enough Credits For Checklist!';
        $view = 'emails.user.not-enough-credits';
        $this->sendTo($user->email, $user->name, $subject, $view, compact('user'));
    }

}