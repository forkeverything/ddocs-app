<template>
    <div id="login" class="container">
        <div class="row">
            <div id="login-body" class="col-sm-6 col-sm-offset-3">
                <h2 class="text-center">Login</h2>

                <form-errors></form-errors>

                <form id="form-login" role="form" @submit.prevent="submitLogin">

                    <div class="form-group" :class="{ 'has-error': false }">
                        <label for="email" class="control-label">Email</label>
                        <input id="email"
                               type="email"
                               class="form-control"
                               v-model="email"
                        >


                    </div>

                    <div class="form-group" :class="{ 'has-error': false }">
                        <label for="password" class="control-label">Password</label>
                        <input id="password" type="password" class="form-control" v-model="password">
                    </div>

                    <div class="form-group">
                        <router-link to="/password/reset">Forgot password?</router-link>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary form-control">
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
        methods: {
            submitLogin(){

                vueClearValidationErrors();
                if (!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.post('/auth/login', {
                    email: this.email,
                    password: this.password
                }).then((response) => {
                    AuthCookie.store(response.json().token);
                    store.dispatch('fetchAuthenticatedUser');
                    router.push('/');
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
        }
    }
</script>