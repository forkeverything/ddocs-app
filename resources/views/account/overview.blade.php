@extends('layouts.app')

@section('content')
    <div id="account-overview" class="container">
        <h1 class="text-center">Your Account</h1>
        <div id="account-details">
            <h3>Your Details</h3>
            <h4>Name</h4>
            <p>
                {{ $user->name }} <em class="text-muted"> - As seen by recipients</em>
            </p>
            <h4>Email</h4>
            {{ $user->email }}

            <hr>

            <h3>Credits & Subcription</h3>
            <h4>Credits Remaining</h4>
            <p>{{ $user->credits }}</p>
            <p class="text-muted">
                1 Credit = 1 List. You get 5 free credits at the beginning of each month.
                <br>
                Earn more credits by asking your list recipients to sign up for an account!</p>
            <h4>Subscription</h4>
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
                <h4>Why Subscribe?</h4>
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
