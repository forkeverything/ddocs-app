<template>
    <div id="reset-form" class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <br>
                <h2 class="text-center">Reset Password</h2>
                <br>
                <div class="alert alert-danger text-center" v-if="error">
                    {{ error }}
                </div>
                <form-errors :stealth="true" @got-error="gotError"></form-errors>

                <form role="form" @submit.prevent="resetPassword">
                    <div class="form-group"
                         :class="{
                                        'has-error': formErrors.email
                                     }"
                    >
                        <label for="field-email" class="control-label">E-Mail Address</label>
                        <input id="field-email" type="email"
                               class="form-control"
                               v-model="email"
                               autofocus
                        >
                        <span class="help-block" v-if="formErrors.email">
                                            <strong>{{ formErrors.email[0] }}</strong>
                                        </span>
                    </div>
                    <div class="form-group"
                         :class="{
                                        'has-error': formErrors.password
                                     }"
                    >
                        <label for="field-password" class="control-label">Password</label>

                        <input id="field-password"
                               type="password"
                               class="form-control"
                               v-model="password"
                        >

                        <span class="help-block" v-if="formErrors.password">
                                            <strong>{{ formErrors.password[0] }}</strong>
                                        </span>
                    </div>
                    <div class="form-group"
                         :class="{
                                        'has-error': formErrors.password_confirmation
                                     }"
                    >
                        <label for="field-password-confirm" class="control-label">
                            Confirm Password
                        </label>
                        <input id="field-password-confirm"
                               type="password"
                               class="form-control"
                               v-model="passwordConfirmation"
                        >
                        <span class="help-block" v-if="formErrors.password_confirmation">
                                            <strong>{{ formErrors.password_confirmation[0] }}</strong>
                                        </span>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary form-control">
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
    const CatchFormErrors = require('../../mixins/catch-form-errors');

    export default {
        data: function () {
            return {
                ajaxReady: true,
                email: '',
                password: '',
                passwordConfirmation: '',
                error: ''
            }
        },
        methods: {
            resetPassword(){
                vueClearValidationErrors();
                this.error = '';
                if (!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.post('/password/reset', {
                    token: this.$route.params.token,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.passwordConfirmation
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    Authenticator.login(response.json());
                    this.ajaxReady = true;
                }, (response) => {
                    // error
                    console.log("error posting to: /password/reset");
                    if (response.status === 400) this.error = response.json().error;
                    this.ajaxReady = true;
                    vueValidation(response);
                });
            }
        },
        mixins: [CatchFormErrors]
    }
</script>