<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Requests;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.auth', [
            'only' => [
                'getAuthenticatedUser'
            ]
        ]);
    }

    /**
     * Returns the authenticated User from the token provided.
     *
     * @return mixed
     */
    public function getAuthenticatedUser()
    {
        return Auth::user();
    }

    /**
     * Attempt to create a token from the credentials provided.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postAttemptLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(["auth" => ["Email and password doesn't match an account. Please try again."]], 422);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }
}
