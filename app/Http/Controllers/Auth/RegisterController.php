<?php

namespace App\Http\Controllers\Auth;

use App\Auth\HandleRefreshToken;
use App\Checklist;
use App\Events\RecipientClaimedInvitation;
use App\Recipient;
use App\User;
use Auth;
use Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers, HandleRefreshToken;

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
        $this->middleware('guest', [
            'except' => [
                'putUpdateUser'
            ]
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }


    /**
     * Return a token instead of redirect.
     *
     * @param  \Illuminate\Http\Request $request
     * @return User|\Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $token = $this->guard()->login($user = $this->create($request->all()));

        $checklist = null;

        $this->claimFreeCredits($user, $request->invite_key);

        return $this->refreshTokenResponse($token, $user);

    }

    /**
     * Save changes to authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function putUpdateUser(Request $request)
    {
        if (Auth::user()->update($request->all())) return response('updated user.');
        return response('failed updating user', 500);
    }

    /**
     * Claim free credits when registered from sign-up offer on checklist page.
     *
     * @param User $recipientUser
     * @param $inviteKey
     */
    protected function claimFreeCredits(User $recipientUser, $inviteKey)
    {
        if ($inviteKey && $checklist = Checklist::findByHash($inviteKey)) $checklist->claimInvite($recipientUser);;
    }
}
