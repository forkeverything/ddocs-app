<?php

namespace App\Http\Controllers\Auth;

use App\Checklist;
use App\Events\NewUserSignedUp;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/checklist';

    /**
     * Create a new authentication controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
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
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);


        Event::fire(new NewUserSignedUp($user));

        return $user;
    }

    /**
     * Over-write show registration form method to get invite key (checklist hash)
     * from url
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm(Request $request)
    {

        $inviteKey = $request->invite_key;
        
        if (property_exists($this, 'registerView')) {
            return view($this->registerView, compact('inviteKey'));
        }

        return view('auth.register', compact('inviteKey'));
    }


    /**
     * Over-write to add credits if valid inviteKey provided. When User has
     * claimed offer to sign-up as a recipient of a checklist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        // create user
        $user = $this->create($request->all());

        $this->addInviteCredits($request->invite_key, $user);

        Auth::guard($this->getGuard())->login($user);

        return redirect($this->redirectPath());
    }

    /**
     * Add credits to the recipient and person who made the Checklist.
     *
     * @param $inviteKey
     * @param User $recipient
     */
    protected function addInviteCredits($inviteKey, User $recipient)
    {
        $checklist = Checklist::find(unhashId($inviteKey));
        if ($checklist) {
            $checklist->claimInvite($recipient);
        }
    }
}
