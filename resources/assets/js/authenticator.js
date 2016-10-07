module.exports = {

    /**
     * Path to redirect to after getting new token.
     */

    _redirectPath: '',

    /**
     * Checks response for a token in the header. Implies a token has been
     * refreshed and we'll need to save the new one for subsequent
     * reqeusts.
     *
     * @param response
     */

    _refreshToken(response) {
        let newToken = response.headers.authorization;
        if(newToken) {
            this._storeCookie(newToken);
        }
    },

    /**
     * Checks to see if our response is for an invalid or expired token. This is where
     * the user gets redirected.
     *
     * @param response
     */

    _checkForAuthError(response) {

        let errors = [
            'unauthenticated',      // either default 'auth' middleware or didn't get a token
            'token_invalid',
            'token_expired',
            'token_revoked'
        ];

        if(response.status === 401 && errors.indexOf(response.json().error) !== -1) {
            this._removeCookie();
            this._unsetAuthenticatedUser();
            if(router.currentRoute.meta.requiresAuth) {
                this._redirectToLogin();
            }
        }

    },

    /**
     * Need user to re-login.
     */

    _redirectToLogin(){

        // Clear all pending requests
        RequestsMonitor.flushQueue();

        // save where the user is currently at
        this._redirectPath = router.currentRoute.fullPath;
        router.push('/login');
    },

    /**
     * Store our token in a cookie
     *
     * @param token
     */

    _storeCookie(token){
        // store a cookie so it'll be read on refresh
        Cookies.set('ddocs_auth', token);
    },

    /**
     * Set the header for vue-resource as well as jQuery's AJAX functions. Since
     * we'll only be making requests as an authenticated user after this
     * point, we'll go ahead and tell Vuex to get the user here too.
     *
     * @param token
     */

    _setHeaders(token) {
        Vue.http.headers.common['Authorization'] = token;
        this._fetchAuthenticatedUser();
    },

    /**
     * Fetch auth cookie
     */

    _getCookie() {
        return Cookies.get('ddocs_auth');
    },

    /**
     * Remove our auth cookie
     */

    _removeCookie(){
        return Cookies.remove('ddocs_auth');
    },

    /**
     * If user was redirected to login (and we've saved their redirect
     * path), we'll want to redirect them back there.
     */

    _goRedirectPath(){
        if(this._redirectPath) {
            router.push(this._redirectPath);
            this._redirectPath = '';
        } else {
            router.push('/');
        }
    },

    /**
     * HTTP Interceptor that we'll use to catch all responses and pass
     * them onto our auth object to handle token related responses.
     */

    _pushResourceInterceptor(){
        Vue.http.interceptors.push((request, next) => {

            // add unique id for client-side look-up
            request._uid = randomString(10);

            next((response) => {

                // Request is complete here. Remove from global queue
                RequestsMonitor.removeFromQueue(request);

                this._refreshToken(response);
                this._checkForAuthError(response);
            });
        });
    },

    /**
     * Tell Vuex to fetch and set the authenticated user.
     */

    _fetchAuthenticatedUser(){
        store.dispatch('fetchAuthenticatedUser');
    },

    /**
     * Unset the user from our Vuex store so
     * we'll go back to being a guest.
     */

    _unsetAuthenticatedUser(){
        store.commit('setUser', '');
    },

    /**
     * Setup everything up on page-load. This only gets fired once
     * and is called within bootstrap.js
     */

    setup(){

        // set our interceptor - this has to happen first!
        this._pushResourceInterceptor();

        // If we found a cookie (logged-in)
        if(this._getCookie()) {
            // Set our request headers
            this._setHeaders(this._getCookie());
        }
    },

    /**
     * Check if there is an authenticated User.
     *
     * @returns {boolean}
     */

    check(){
        return !! this._getCookie();
    },

    /**
     * Login user after receiving token
     */

    login(loginResponse){
        let token = 'Bearer ' + loginResponse.token;
        this._storeCookie(token);
        this._setHeaders(token);
        this._goRedirectPath();
    },

    /**
     * Logout authenticated user
     */

    logout(){
        Vue.http.post('/logout').then((res) => {
            this._removeCookie();
            delete Vue.http.headers.common["Authorization"];
            this._unsetAuthenticatedUser();
            router.push('/login');
        }, (res) => {
            console.log("couldn't log out user");
        });
    }
};
