<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

/**
 * Controller Single Logged-in User's Account.
 * Class AccountController
 *
 * @package App\Http\Controllers
 */
class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the overview view for accounts.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAccountOverview()
    {
        $user = Auth::user();
        return view('account.overview', compact('user'));
    }

    /**
     * Handle POST Request to active subscription after getting a
     * Stripe credit card token.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function postSubscribe(Request $request)
    {
        $user = Auth::user();
        if($user->subscribed('main')) abort(409, "Already subscribed");
        $subscription = $user->newSubscription('main', 'monthly')->create($request->credit_card_token);
        return response("Subscribed");
    }

    /**
     * Cancel authenticated user's subcription.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteCancelSubscription()
    {
        $user = Auth::user();
        if(! $user->subscribed('main')) abort(409, "Already subscribed");
        $user->subscription('main')->cancel();
        return redirect()->back();
    }

    /**
     * POST request to resume subscription.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postResumeSubscription()
    {
        Auth::user()->subscription('main')->resume();
        return redirect()->back();
    }
}
