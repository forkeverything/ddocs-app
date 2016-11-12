<?php

namespace App\Http\Controllers;

use App\Coupon;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Validator;

/**
 * Controller Single Logged-in User's Account.
 * Class AccountController
 *
 * @package App\Http\Controllers
 */
class AccountController extends Controller
{

    /**
     * Change User Password
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function postChangePassword(Request $request)
    {
        Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6'
        ])->validate();

        $validPassword = Auth::guard()->validate([
            'email' => Auth::user()->email,
            'password' => $request->current_password,
        ]);

        if($validPassword) {
            Auth::user()->update([
                'password' => bcrypt($request->new_password)
            ]);
            return response('Updated password.');
        } else {
            return response()->json([
                'error' => [
                    "Sorry, your current password is incorrect."
                ]
            ], 422);
        }
    }

//    /**
//     * Handle POST Request to active subscription after getting a
//     * Stripe credit card token.
//     *
//     * @param Request $request
//     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
//     */
//    public function postSubscribe(Request $request)
//    {
//        $user = Auth::user();
//        if($user->subscribed('main')) abort(409, "Already subscribed");
//        $subscription = $user->newSubscription('main', 'monthly')->create($request->credit_card_token);
//        return response("Subscribed");
//    }
//
//    /**
//     * Cancel authenticated user's subcription.
//     *
//     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
//     */
//    public function deleteCancelSubscription()
//    {
//        $user = Auth::user();
//        if(! $user->subscribed('main')) abort(409, "Already subscribed");
//        $user->subscription('main')->cancel();
//        return redirect()->back();
//    }
//
//    /**
//     * POST request to resume subscription.
//     *
//     * @return \Illuminate\Http\RedirectResponse
//     */
//    public function postResumeSubscription()
//    {
//        Auth::user()->subscription('main')->resume();
//        return redirect()->back();
//    }
//
//    /**
//     * Have the currently authenticated User claim
//     * a coupon.
//     *
//     * @return \Illuminate\Http\RedirectResponse
//     * @throws \App\Exceptions\InvalidCouponCode
//     */
//    public function postClaimCoupon()
//    {
//        Coupon::findByCode(request('coupon_code'))->claim(Auth::user());
//        return redirect()->back();
//    }
}
