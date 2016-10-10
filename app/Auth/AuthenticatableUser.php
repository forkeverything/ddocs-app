<?php

namespace App\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject as JWTAuthContract;

/**
 * This is a base class to be extended by our User model. It's pretty much the
 * same as Laravel's Illuminate\Foundation\Auth\User Class, but we've added
 * a JWTSubject Contract and methods to handle authentication using JWT.
 *
 * @package App
 */

class AuthenticatableUser extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    JWTAuthContract
{
    use Authenticatable,
        Authorizable,
        CanResetPassword;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'user' => [
                'id' => $this->id
            ]
        ];
    }
}