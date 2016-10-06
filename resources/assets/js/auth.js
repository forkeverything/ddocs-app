module.exports = {

    /**
     * Path to redirect to after getting new token.
     */

    redirectPath: '',

    /**
     * Checks response for a token in the header. Implies a token has been
     * refreshed and we'll need to save the new one for subsequent
     * reqeusts.
     *
     * @param response
     */

    refreshToken(response) {
        let newToken = response.headers.authorization;
        if(newToken) {
            this.storeCookie(newToken);
        }
    },

    /**
     * Checks to see if our response is for an invalid or expired token. This is where
     * the user gets redirected.
     *
     * @param response
     */

    checkForAuthError(response) {

        let errors = [
            'unauthenticated',
            'token_invalid',
            'token_expired',
            'token_revoked'
        ];

        if(response.status === 401 && errors.indexOf(response.json().error) !== -1) {
            this.removeCookie();
            this.unsetAuthenticatedUser();
            if(router.currentRoute.meta.requiresAuth) {
                this.redirectToLogin();
            }
        }

        // TODO ::: return specific token errors instead of generic unauthenticated
    },

    /**
     * Need user to re-login.
     */

    redirectToLogin(){

        // Clear all pending requests
        RequestsMonitor.flushQueue();

        // save where the user is currently at
        this.redirectPath = router.currentRoute.fullPath;
        router.push('/login');
    },

    /**
     * Store our token in a cookie
     *
     * @param token
     */

    storeCookie(token){
        // store a cookie so it'll be read on refresh
        Cookies.set('ddocs_auth', token);
        // Assume storing a valid token so we'll set our header here.
        this.setHeaders(token);
    },

    /**
     * Set the header for vue-resource as well as jQuery's AJAX functions. Since
     * we'll only be making requests as an authenticated user after this
     * point, we'll go ahead and tell Vuex to get the user here too.
     *
     * @param token
     */

    setHeaders(token) {
        Vue.http.headers.common['Authorization'] = token;
        this.fetchAuthenticatedUser();
    },

    /**
     * Fetch auth cookie
     */

    getCookie() {
        return Cookies.get('ddocs_auth');
    },

    /**
     * Remove our auth cookie
     */

    removeCookie(){
        return Cookies.remove('ddocs_auth');
    },

    /**
     * If user was redirected to login (and we've saved their redirect
     * path), we'll want to redirect them back there.
     */

    goRedirectPath(){
        if(this.redirectPath) {
            router.push(this.redirectPath);
            this.redirectPath = '';
        } else {
            router.push('/');
        }
    },

    /**
     * HTTP Interceptor that we'll use to catch all responses and pass
     * them onto our auth object to handle token related responses.
     */

    pushResourceInterceptor(){
        Vue.http.interceptors.push((request, next) => {

            // add unique id for client-side look-up
            request._uid = randomString(10);

            next((response) => {

                // Request is complete here. Remove from global queue
                RequestsMonitor.removeFromQueue(request);

                // this.refreshToken(response);
                this.checkForAuthError(response);
                // return response;
            });
        });
    },

    /**
     * Setup everything up on page-load. This only gets fired once
     * and is called within bootstrap.js
     */

    setup(){

        // set our interceptor - this has to happen first!
        this.pushResourceInterceptor();

        // If we found a cookie (logged-in)
        if(this.getCookie()) {
            // Set our request headers
            this.setHeaders(this.getCookie());
        }
    },

    /**
     * Logout authenticated user
     */

    logout(){
        Vue.http.post('/logout').then((res) => {
            this.removeCookie();
            delete Vue.http.headers.common["Authorization"];
            this.unsetAuthenticatedUser();
            router.push('/login');
        }, (res) => {
            console.log("couldn't log out user");
        });
    },

    /**
     * Tell Vuex to fetch and set the authenticated user.
     */

    fetchAuthenticatedUser(){
        store.dispatch('fetchAuthenticatedUser');
    },

    /**
     * Unset the user from our Vuex store so
     * we'll go back to being a guest.
     */

    unsetAuthenticatedUser(){
        store.commit('setUser', '');
    }
};
