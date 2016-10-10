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
     * Extend user's refresh token by 14 days.
     *
     * @param User $user
     * @return User
     */
    protected function extendRefreshTokenForUser(User $user)
    {
        $user->refresh_token_expiry = strtotime("+14 day");
        $user->save();
        return $user;
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

        // The refresh token is still good! Let's extend the expiry and
        // return the User back.
        return $this->extendRefreshTokenForUser($user);
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

    /**
     * Set a new refresh token for User. Only called after
     * successful login or registration.
     *
     * @param User $user
     * @return mixed
     */
    protected function removeRefreshToken(User $user)
    {
        $user->refresh_token = null;
        $user->refresh_token_expiry = null;
        return $user->save();
    }

    /**
     * Make a response that has both auth token and the refresh
     * token for given User. This is the response that is
     * sent after successful login / registration.
     * 
     * @param $token
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function makeTokenResponse($token, User $user)
    {
        return response()->json([
            'token' => $token,
            'refresh_token' => $this->setRefreshToken($user)
        ]);
    }

}