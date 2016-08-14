@extends('layouts.app')

@section('fb-track')
    <script>
        fbq('track', 'ViewAccountOverview');
    </script>
@endsection

@section('content')
    <account-overview inline-template v-cloak>
        <div id="account-overview" class="container">
            <h1 class="text-center">Your Account</h1>
            <div id="account-details">
                <h3>Your Details</h3>
                <h5>Name</h5>
                <p>
                    {{ $user->name }} <em class="text-muted"> - As seen by recipients</em>
                </p>
                <h5>Email</h5>
                {{ $user->email }}

                <hr>

                <h3>Credits & Subcription</h3>
                <h5>Credits Remaining</h5>
                <p>{{ $user->credits }}</p>
                <p class="text-muted">
                    1 Credit = 1 List. You get 5 free credits at the beginning of each month.
                    <br>
                    Earn more credits by asking your list recipients to sign up for an account!</p>
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
                                Subscription</button>
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
                    <button type="button" class="btn btn-solid-green" @click="toggleCreditCardForm" v-show="
                    ! showCreditCardForm">Sure, I can do 5 bucks</button>

                    <form id="form-credit-card" @submit.prevent="processCard" v-el:stripe-form
                          v-show="showCreditCardForm">
                        <hr>
                        <h3>Add Credit Card</h3>
                        <p>After you add a card with us we'll bill you $15 monthly.
                            <br>
                            You can <strong>cancel at any time</strong> and your subscription will still be active until
                            the end of the billing cycle.</p>
                        <div class="form-group">
                            <label>Card Number</label>
                            <input data-stripe="number" type="text" required size="20" v-model="ccNumber"
                                   class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Cardholder's Name</label>
                            <input data-stripe="name" type="text" required v-model="ccName" class="form-control">
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-8">
                                <label>Expiry Date</label>
                                <div class="expiry-fields">
                                    <input data-stripe="exp_month" type="text" required size="2" v-model="ccExpMonth"
                                           placeholder="MM" class="form-control"><span class="separator">/</span><input
                                            data-stripe="exp_year" type="text" required size="4" v-model="ccExpYear"
                                            placeholder="YYYY" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label>CVC</label>
                                <input data-stripe="cvc" type="text" required size="4" v-model="ccCVC"
                                       class="form-control">
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <button type="button" class="btn btn-outline-grey btn-space" @click="toggleCreditCardForm">
                            Cancel</button>
                            <button type="submit" class="btn btn-solid-green"
                                    :disabled="! canSubmit">@{{ addCardButtonText }}</button>
                        </div>
                    </form>

                @endif
            </div>


        </div>
    </account-overview>
@endsection
