<?php

namespace App\Http\Controllers;

use App\Auth\HandleRefreshToken;
use Illuminate\Http\Request;

use App\Http\Requests;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTController extends Controller
{
    use HandleRefreshToken;

    public function refresh(Request $request)
    {
        $user = $this->validateRefreshToken($request->refresh_token);
        // Generate a new token from user
        return response()->json([
            'token' => JWTAuth::fromUser($user)
        ]);
    }
}
