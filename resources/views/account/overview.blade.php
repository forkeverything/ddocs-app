@extends('layouts.app')

@section('content')
    <div id="account-overview" class="container">
        <h3><strong>Account Settings</strong></h3>
        <div id="account-details">
            <h4><strong>Details</strong></h4>
            <h5>Name</h5>
            <p>
                {{ $user->name }}
                <br>
                <em class="text-muted small">What your recipients will see</em>
            </p>
            <h5>Email</h5>
            <p>
                {{ $user->email }}
            </p>

            <br>

            <h4><strong>Coupon</strong></h4>
            <p>Enter your code here to get free credits</p>
            <div class="row">
                <div class="col-sm-4">
                    <form action="/account/coupon" method="POST">
                        {{ csrf_field() }}


                        <div class="form-group @if($errors->has('coupon')) has-error with-helper @endif">
                            <label class="control-label" for="field-coupon-code">Code</label>
                            <input id="field-coupon-code" type="text" name="coupon_code" class="form-control"
                                   value="{{ old('coupon_code') }}">

                            @if($errors->has('coupon'))
                                <span class="help-block">
                                {{ $errors->first('coupon') }}
                            </span>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-default btn-sm">Claim</button>
                    </form>
                </div>
            </div>

            <br>

            <h4><strong>Credits & Subcription</strong></h4>
            <h5>Credits Remaining</h5>
            <span>{{ $user->credits }}</span>
            <br>
            <em class="text-muted small">
                1 Credit = 1 List. You get 5 free credits at the beginning of each month.
                <br>
                Earn more credits by asking your list recipients to sign up for an account!</em>
            <br>
            <h5>Subscription</h5>
            @if($user->subscribed('main'))
                <p>
                    $15/month - Unlimited Lists
                </p>
                @if($user->subscription('main')->onGracePeriod())
                    <p class="text-muted">
                        Cancelled
                        <br>
                        Expiring on: {{ $user->subscription('main')->ends_at->format('d M Y') }}
                    </p>
                    <form action="/account/subscription/resume" method="POST">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-solid-blue">Resume Subscription</button>
                    </form>
                @else
                    <form action="/account/subscription" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-outline-red">Cancel
                            Subscription
                        </button>
                    </form>
                @endif

            @else
                <p>
                    Inactive
                </p>
                <h5>Why Subscribe?</h5>
                <p>
                    If you make a ton of lists or just enjoy our service, you can help <strong>keep us around for
                        only
                        $15 / month</strong>.
                    <br>
                    You'll get <strong>unlimited lists</strong>, and you'll still get to <strong>keep your
                        credits</strong> if you decide to cancel your subscription.
                </p>

                <add-credit-card></add-credit-card>

            @endif
        </div>
    </div>
@endsection
