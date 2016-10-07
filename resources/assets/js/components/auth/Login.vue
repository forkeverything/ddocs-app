<template>
    <div id="login" class="container">
        <div class="row">
            <div id="login-body" class="col-sm-6 col-sm-offset-3">
                <h2 class="text-center">Login</h2>

                <form-errors @got-error="gotError" stealth="true"></form-errors>

                <form id="form-login" role="form" @submit.prevent="submitLogin">

                    <div class="form-group"
                         :class="{ 'has-error': formErrors.email }"
                    >
                        <label for="email" class="control-label">Email</label>
                        <input id="email"
                               type="email"
                               class="form-control"
                               v-model="email"
                        >
                        <span class="help-block" v-if="formErrors.email">
                            {{ formErrors.email[0] }}
                        </span>
                    </div>

                    <div class="form-group"
                         :class="{ 'has-error': formErrors.password }"
                    >
                        <label for="password" class="control-label">Password</label>
                        <input id="password" type="password" class="form-control" v-model="password">
                        <span class="help-block" v-if="formErrors.password">
                            {{ formErrors.password[0] }}
                        </span>
                    </div>

                    <div class="form-group text-right">
                        <router-link to="/password/reset">Forgot password?</router-link>
                    </div>

                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary" :disabled="! canSubmit">
                            <i class="fa fa-btn fa-sign-in"></i> Login
                        </button>
                    </div>

                    <div class="form-group text-right">
                        <p class="text-muted text-right">Don't have an account yet?
                            <router-link to="/register">Sign up</router-link>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                ajaxReady: true,
                email: '',
                password: ''
            }
        },
        computed: {
            canSubmit(){
                return this.email && this.password;
            }
        },
        methods: {
            submitLogin(){

                vueClearValidationErrors();
                if (!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.post('/login', {
                    email: this.email,
                    password: this.password
                }).then((response) => {
                    Authenticator.login(response.json());
                    // success
                    this.ajaxReady = true;
                }, (response) => {
                    // error
                    console.log('GET REQ Error!');
                    console.log(response);
                    this.ajaxReady = true;
                    vueValidation(response);
                })


            }
        },
        mixins: [
            require('../../mixins/catch-form-errors')
        ]
    }
</script>