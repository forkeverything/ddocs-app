<template>
    <div id="register" class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h2 class="text-center">Create Account</h2>
                <br>

                <form-errors @got-error="gotError" stealth="true"></form-errors>

                <form class="form-horizontal" role="form" @submit.prevent="register">

                    <div class="form-group"
                         :class="{
                            'has-error': formErrors.name
                         }"
                    >
                        <label for="name" class="col-md-4 control-label">Name</label>
                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control" v-model="name">
                            <span class="help-block" v-if="formErrors.name">
                                {{ formErrors.name[0] }}
                            </span>
                        </div>
                    </div>

                    <div class="form-group"
                         :class="{
                            'has-error': formErrors.email
                         }"
                    >
                        <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                        <div class="col-md-6">
                            <input id="email" type="email" class="form-control" v-model="email">
                            <span class="help-block" v-if="formErrors.email">
                                {{ formErrors.email[0] }}
                            </span>
                        </div>
                    </div>

                    <div class="form-group"
                         :class="{
                            'has-error': formErrors.password
                         }"
                    >
                        <label for="password" class="col-md-4 control-label">Password</label>
                        <div class="col-md-6">
                            <input id="password" type="password" class="form-control" v-model="password">
                            <span class="help-block" v-if="formErrors.password">
                                {{ formErrors.password[0] }}
                            </span>
                        </div>
                    </div>

                    <div class="form-group"
                         :class="{
                            'has-error': formErrors.password_confirmation
                         }"
                    >
                        <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                        <div class="col-md-6">
                            <input id="password-confirm"
                                   type="password"
                                   class="form-control"
                                   v-model="password_confirmation"
                            >
                            <span class="help-block" v-if="formErrors.password_confirmation">
                                {{ formErrors.password_confirmation[0] }}
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4 text-right">
                            <button type="submit" class="btn btn-success " :disabled="! canSubmit">
                                <i class="fa fa-btn fa-user"></i> Register
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <p class="text-muted text-right">Already have an account?
                                <router-link to="/login">Login</router-link>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Register',
        data: function () {
            return {
                inviteKey: '',
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            }
        },
        computed: {
            canSubmit(){
                return this.name && this.email && this.password && this.password_confirmation;
            }
        },
        methods: {
            register() {
                this.$http.post('/register', {
                    invite_key: this.inviteKey,
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation
                }).then((res) => {
                    Authenticator.login(res.json());
                    this.ajaxReady = true;
                }, (res) => {
                    this.ajaxReady = true;
                    vueValidation(res);
                })
            }
        },
        mixins: [
            require('../../mixins/catch-form-errors')
        ]
    };
</script>