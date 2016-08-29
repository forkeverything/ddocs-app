<template>
    <button type="button" class="btn btn-solid-green" @click="toggleCreditCardForm" v-show="
                    ! showCreditCardForm">Sure, I can do 15 bucks</button>

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
                    :disabled="! canSubmit">{{ addCardButtonText }}</button>
        </div>
    </form>
</template>
<script>
export default {
    name: 'addCreditCard',
    data: function(){
        return {
            ajaxReady: true,
            showCreditCardForm: false,
            cardError: '',
            waitingStripeResponse: false,
            ccName: '',
            ccNumber: '',
            ccExpMonth: '',
            ccExpYear: '',
            ccCVC: ''
        }
    },
    computed: {
        addCardButtonText: function() {
            if(this.waitingStripeResponse) return 'Processing...';
            return 'Add Card';
        },
        canSubmit: function() {
            return ! this.waitingStripeResponse && this.ccName && this.ccNumber && this.ccExpMonth && this.ccExpYear && this.ccCVC;
        }
    },
    methods: {
        toggleCreditCardForm: function() {
            this.showCreditCardForm = !this.showCreditCardForm;
        },
        processCard: function() {
            var self = this;

            // prevent request pile ups...
            if(self.waitingStripeResponse) return;
            self.waitingStripeResponse = true;

            var $form = $(self.$els.stripeForm);
            self.cardError = '';

            // Make request to stripe
            Stripe.card.createToken($form, function(status, response) {
                if(response.error) {
                    // Card error
                    self.cardError = response.error;
                    self.waitingStripeResponse = false;
                } else {
                    // Successfully got credit card token - subscribe user
                    self.$http.post('/account/subscription', {
                        'credit_card_token': response.id
                    }).then((response) => {
                        location.reload();
                    }, (response) => {
                        console.log(response);
                        self.waitingStripeResponse = false;
                    });
                }
            });
        }
    },
    events: {

    },
    ready: function() {
        Stripe.setPublishableKey($('meta[name="stripe-key"]').attr('content'));
    }
}
</script>