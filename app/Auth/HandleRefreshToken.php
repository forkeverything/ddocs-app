<?php

namespace App\Auth;

use App\Exceptions\RefreshTokenExpired;
use App\Exceptions\RefreshTokenInvalid;
use App\Exceptions\RefreshTokenRevoked;
use App\User;
use Illuminate\Support\Str;

trait HandleRefreshToken
{

    /**
     * Generate our token
     *
     * @return string
     */
    protected function generateToken()
    {
        return Str::random(60);
    }

    /**
     * Check if given token is valid for given User.
     *
     * @param $token
     * @return bool
     * @throws RefreshTokenExpired
     * @throws RefreshTokenInvalid
     * @throws RefreshTokenRevoked
     */
    protected function validateRefreshToken($token = null)
    {
        if(! $token) throw new RefreshTokenInvalid;
        if(! $user = $this->getUserByRefreshToken($token)) throw new RefreshTokenRevoked;
        if ($user->refresh_token_expiry < strtotime('now')) throw new RefreshTokenExpired;

        // the refresh token is still good, let's give back the user we found so we
        // can issue a new JWT.
        return $user;
    }

    /**
     * Fetches a User from a refresh token. If user has re-logged-in or
     * logged-out then we wouldn't be able to find a User here.
     * Likewise if the token was tampered with.
     *
     * @param $token
     * @return mixed
     */
    protected function getUserByRefreshToken($token)
    {
        return User::where('refresh_token', $token)->first();
    }

    /**
     * Set a new refresh token for User. Only called after
     * successful login or registration.
     *
     * @param User $user
     * @return mixed
     */
    protected function setRefreshToken(User $user)
    {
        $token = $this->generateToken();
        $user->refresh_token = $token;
        $user->refresh_token_expiry = strtotime("+14 day");
        $user->save();
        return $token;
    }

}