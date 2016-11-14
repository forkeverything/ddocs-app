<template>
    <div id="email-reset-pw" class="container">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="alert alert-success text-center" v-if="status">
                {{ status }}
            </div>
            <h2 class="text-center">Send Reset Password Link</h2>
            <p class="text-center">
                Enter your email and we'll send you a link to reset your password.
            </p>
            <form-errors :stealth="true" @got-error="gotError"></form-errors>
            <form class="form-horizontal" role="form" method="POST" @submit.prevent="submitForm">
                <div class="col-md-6 col-md-offset-3">
                    <div class="form-group"
                         :class="{
                                    'has-error': formErrors.email
                                 }"
                    >
                        <input id="input-email" type="email" class="form-control" v-model="email"
                               placeholder="Account email address">
                        <span class="help-block" v-if="formErrors.email">
                                <strong>{{ formErrors.email[0] }}</strong>
                            </span>
                    </div>
                    <div class="form-group text-center">
                        <button id="btn-send" type="submit" class="btn btn-info">
                            <span v-if="ajaxReady">Send <i class="fa fa-paper-plane"></i></span>
                            <span v-if="! ajaxReady">Sending...</span>
                        </button>
                    </div>
                    <div class="text-right">
                        <router-link to="/login">Back to login</router-link>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
    const CatchFormErrors = require('../../mixins/catch-form-errors');
    export default {
        name: 'SendResetPasswordLink',
        data: function () {
            return {
                ajaxReady: true,
                status: '',
                email: ''
            }
        },
        methods: {
            submitForm(){
                vueClearValidationErrors();
                if (!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.post('/password/email', {
                    email: this.email
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.status = response.json().status;
                    this.ajaxReady = true;
                }, (response) => {
                    // error
                    console.log("error posting to: /password/email");
                    this.ajaxReady = true;
                    vueValidation(response);
                });
            }
        },
        mixins: [CatchFormErrors]
    }
</script>