Vue.component('account-overview', {
    name: 'AccountOverview',
    el: function() {
        return '#account-overview'
    },
    data: function() {
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
        };
    },
    props: [],
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
                    // Successfully got credit card token
                    $.ajax({
                        url: '/account/subscription',
                        method: 'POST',
                        data: {
                            'credit_card_token': response.id
                        },
                        success: function(data) {
                           // success
                            location.reload();
                        },
                        error: function(response) {
                            console.log(response);
                        }
                    });
                }
            });
        },
        cancelSubscription: function() {
            var self = this;
            if(!self.ajaxReady) return;
            self.ajaxReady = false;
            $.ajax({
                url: '/account/subscription',
                method: 'DELETE',
                success: function(data) {
                   // success
                    location.reload();
                   self.ajaxReady = true;
                },
                error: function(response) {
                    console.log(response);
                    self.ajaxReady = true;
                }
            });
        }
    },
    events: {

    },
    ready: function() {
        Stripe.setPublishableKey($('meta[name="stripe-key"]').attr('content'));
    }
});