<?php


namespace App\Auth;


use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\JWTGuard;

class CustomJWTGuard extends JWTGuard
{
    /**
     * Login User - by returning a token.
     * @param JWTSubject $user
     * @return string
     */
    public function login(JWTSubject $user)
    {
        $this->setUser($user);

        // insert our csrf claim here
        return $this->jwt->customClaims(['csrf' => str_random(32)])->fromUser($user);
    }
}