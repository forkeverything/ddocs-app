<?php

namespace App\Http\Controllers\Auth;

use App\Auth\HandleRefreshToken;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, HandleRefreshToken;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }


    /**
     * Keep throttling but we'll want to save the token that attempt()
     * return and also pass that token on to our response.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($lockedOut = $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        $credentials = $this->credentials($request);

        // This is the only part we're over-riding from the trait. We need to fetch
        // the token and also deprecate $request->has('remember') param.
        if ($token = $this->guard()->attempt($credentials)) {
            return $this->sendLoginResponse($request, $token);
        }

        if (!$lockedOut) {
            $this->incrementLoginAttempts($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Send the response after the user was authenticated. We don't
     * need to start a session or redirect.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request, $token)
    {
        $this->clearLoginAttempts($request);
        return $this->authenticated($request, $this->guard()->user(), $token);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user, $token)
    {
        return $this->makeTokenResponse($token, $user);
    }

    /**
     * We want to responsd with JSON instead of a redirect.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errorBag = [];

        $validEmail = !!\App\User::where('email', $request->email)->first();

        if (!$validEmail) {
            $errorBag = [
                'email' => [
                    "Couldn't find an account for that email"
                ]
            ];
        } else {
            $errorBag = [
                'password' => [
                    "That password doesn't look right"
                ]
            ];
        }

        return response()->json($errorBag, 422);
    }

    /**
     * Logout when using JWT instead of sessions
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        if ($user = $this->validateRefreshToken($request->refresh_token)) ($this->removeRefreshToken($user));
        $this->guard()->logout();
        return response()->json(['logged out!']);
    }

    /**
     * Refresh our auth token using a refresh token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        $user = $this->validateRefreshToken($request->refresh_token);
        // Generate a new token from user
        return response()->json([
            'token' => JWTAuth::fromUser($user)
        ]);
    }
}

