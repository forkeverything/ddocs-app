<?php

namespace App\Exceptions\Handlers;

trait CouponHandlers
{

    protected function handleInvalidCouponCode($request, \Exception $exception)
    {
        if ($request->ajax()) {
            return response([
                'coupon' => [
                    'Not a valid coupon code.'
                ]
            ], 422);
        }
        return redirect()->back()->withErrors(['coupon' => 'Not a valid coupon code.']);
    }

    public function handleCouponAlreadyClaimed($request, \Exception $exception)
    {
        if ($request->ajax()) {
            return response([
                'coupon' => [
                    'Coupon already claimed.'
                ]
            ], 422);
        }
        return redirect()->back()->withErrors(['coupon' => 'Coupon already claimed.']);
    }

    public function handleRanOutOfCoupon($request, \Exception $exception)
    {
        if ($request->ajax()) {
            return response([
                'coupon' => [
                    'Sorry, We\'ve ran out of that coupon.'
                ]
            ], 422);
        }
        return redirect()->back()->withErrors(['coupon' => 'Sorry, We\'ve ran out of that coupon.']);
    }



}