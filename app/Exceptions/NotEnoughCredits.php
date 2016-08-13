<?php

namespace App\Exceptions;

use App\User;

class NotEnoughCredits extends \Exception
{

    /**
     * The User that doesn't have enough credits.
     *
     * @var User
     */
    public $user;

    public function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
    }

}