/**
 * Client Side Authenticator
 */

module.exports = {

    /**
     * Path to redirect to after getting new token.
     */

    _redirectPath: '',


    _refreshAuthToken(){
        return Vue.http.post('/refresh_token', {
            refresh_token: this._getRefreshToken()
        });
    },

    /**
     * Checks to see if our response is for an invalid or expired token. This is where
     * the user gets redirected.
     *
     * @param request
     * @param response
     */

    _checkAuthentication(request, response) {

        return new Promise((resolve, reject) => {

            let authTokenErrors = [
                'unauthenticated',      // either default 'auth' middleware or didn't get a token
                'token_invalid',
                'token_expired',
                'token_revoked'
            ];

            if (response.status === 401 && authTokenErrors.indexOf(response.json().error) !== -1) {

                // token is bad - lets try to renew it!
                return this._refreshAuthToken().then((refreshTokenResponse) => {

                    let newToken = 'Bearer ' + refreshTokenResponse.json().token;
                    this._storeAuthToken(newToken);
                    request.headers['Authorization'] = newToken;

                    return Vue.http(request).then((newResponse) => {
                        resolve(newResponse);
                    });

                }, (refreshTokenResponse) => {
                    // Failed to refresh our auth token - assume logged out.
                    this._removeRefreshToken();
                    this._removeAuthToken();
                    this._unsetAuthenticatedUser();
                    if (router.currentRoute.meta.requiresAuth) {
                        this._redirectToLogin();
                    }
                    // reject(refreshTokenResponse);
                })

            }

            // if we didn't get any token errors, just pass our response back
            resolve(response);
        });
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

    _getRefreshToken(){
        let refreshToken = localStorage.getItem('ddocs_refresh_token');
        return refreshToken !== 'undefined' ? refreshToken : null;
    },

    /**
     * Store our long-life refresh token used to refresh our auth tokens.
     *
     * @param token
     * @private
     */

    _storeRefreshToken(token) {
        localStorage.setItem('ddocs_refresh_token', token);
    },

    /**
     * Remove refresh token.
     *
     * @private
     */

    _removeRefreshToken(){
        localStorage.removeItem('ddocs_refresh_token');
    },

    /**
     * Fetch auth auth token
     */

    _getAuthToken() {
        let authToken = localStorage.getItem('ddocs_auth_token');
        return authToken !== 'undefined' ? authToken : null;
    },

    /**
     * Store our token in a auth token
     *
     * @param token
     */

    _storeAuthToken(token){
        // store a auth token so it'll be read on refresh
        localStorage.setItem('ddocs_auth_token', token);
    },

    /**
     * Remove our auth auth token
     */

    _removeAuthToken(){
        localStorage.removeItem('ddocs_auth_token');
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
    },

    /**
     * Unset token in header so all subsequent requests are
     * made as a guest.
     *
     * @private
     */
    _unsetHeaders() {
        delete Vue.http.headers.common["Authorization"];
    },

    /**
     * If user was redirected to login (and we've saved their redirect
     * path), we'll want to redirect them back there.
     */

    _goRedirectPath(){
        if (this._redirectPath) {
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

            next((response) => {
                return this._checkAuthentication(request, response).then((response) => {
                    return response;
                });
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
     * Unset the user from our Vuex store.
     */

    _unsetAuthenticatedUser(){
        store.commit('setUser', '');
    },

    /**
     * Turn client back into a Guest.
     *
     * @private
     */
    _revertToGuest(){
        this._removeAuthToken();
        this._removeRefreshToken();
        this._unsetHeaders();
        this._unsetAuthenticatedUser();
        router.push('/login');
    },

    /**
     * Setup everything up on page-load. This only gets fired once
     * and is called within bootstrap.js
     */

    setup() {

        // Set Interceptor. This must occur first for all subsequent
        // requests to have the interceptor applied.
        this._pushResourceInterceptor();

        // User is logged in on page load.
        if (this._getAuthToken()) {
            this._setHeaders(this._getAuthToken());     // Set our request headers
            this._fetchAuthenticatedUser();             // fetch User
        }
    },

    /**
     * Check if there is an authenticated User.
     *
     * @returns {boolean}
     */

    check() {
        return !! this._getAuthToken();
    },

    /**
     * Login the user client-side. The resposne is what we get back
     * from either '/login' or '/register'.
     */

    login(response){

        let authToken = 'Bearer ' + response.token;
        this._storeAuthToken(authToken);
        let refreshToken = response.refresh_token;
        this._storeRefreshToken(refreshToken);

        this._setHeaders(authToken);
        this._fetchAuthenticatedUser();

        this._goRedirectPath();
    },

    /**
     * Logout authenticated user
     */

    logout(){
        Vue.http.post('/logout', {
            refresh_token: this._getRefreshToken()
        }).then((res) => {
            this._revertToGuest();
        }, (res) => {
            this._revertToGuest();
        });
    }
};
