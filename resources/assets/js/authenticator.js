/**
 * Client Side Authenticator
 * Handles all authentication methods and properties.
 *
 * @type {{_redirectPath: string, _refreshAuthToken: (()), _checkAuthentication: ((request?, response?)), _redirectToLogin: (()), _getRefreshToken: (()), _storeRefreshToken: ((token?)), _removeRefreshToken: (()), _getAuthToken: (()), _storeAuthToken: ((token?)), _removeAuthToken: (()), _setHeaders: ((token)), _unsetHeaders: (()), _goRedirectPath: (()), _pushResourceInterceptor: (()), _fetchAuthenticatedUser: (()), _unsetAuthenticatedUser: (()), _revertToGuest: (()), setup: (()), check: (()), login: ((response)), logout: (())}}
 */

module.exports = {

    // Path to redirect to after getting new token.
    _redirectPath: '',

    // POST request to refresh our auth token
    _refreshAuthToken(){
        return Vue.http.post('/refresh_token', {
            refresh_token: this._getRefreshToken()
        });
    },

    // Redirect User if response is for an invalid/expired token.
    _checkAuthentication(request, response) {

        return new Promise((resolve, reject) => {

            let authTokenErrors = [
                'unauthenticated',      // when default 'auth' middleware fails or didn't get a token
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


    // Need User to re-authenticate
    _redirectToLogin(){

        // Clear all pending requests
        RequestsMonitor.flushQueue();

        // save where the user is currently at
        this._redirectPath = router.currentRoute.fullPath;
        router.push('/login');
    },

    // Get our refresh token from local storage
    _getRefreshToken(){
        let refreshToken = localStorage.getItem('ddocs_refresh_token');
        return refreshToken !== 'undefined' ? refreshToken : null;
    },


    // Store our long-life refresh token. Refresh tokens are
    // used to renew auth tokens.
    _storeRefreshToken(token) {
        localStorage.setItem('ddocs_refresh_token', token);
    },


    // Remove refresh token.
    _removeRefreshToken(){
        localStorage.removeItem('ddocs_refresh_token');
    },


    // Get auth token
    _getAuthToken() {
        let authToken = localStorage.getItem('ddocs_auth_token');
        return authToken !== 'undefined' ? authToken : null;
    },


    // Store auth token in local storage
    _storeAuthToken(token){
        // store a auth token so it'll be read on refresh
        localStorage.setItem('ddocs_auth_token', token);
    },


    // Remove our auth token
    _removeAuthToken(){
        localStorage.removeItem('ddocs_auth_token');
    },

    // Set token in request headers for authentication
    _setHeaders(token) {
        Vue.http.headers.common['Authorization'] = token;
    },


    // Unset token in header so all subsequent requests are
    // made as a guest.
    _unsetHeaders() {
        delete Vue.http.headers.common["Authorization"];
    },


    // Redirect User to saved path before redirect or '/'
    _goRedirectPath(){
        if (this._redirectPath) {
            router.push(this._redirectPath);
            this._redirectPath = '';
        } else {
            router.push('/');
        }
    },


    // HTTP Interceptor that we'll use to catch all responses and pass
    // them onto our auth object to handle token related responses.
    _pushResourceInterceptor(){
        Vue.http.interceptors.push((request, next) => {

            next((response) => {
                return this._checkAuthentication(request, response).then((response) => {
                    return response;
                });
            });
        });
    },


    // Tell Vuex to fetch and set the authenticated user.
    _fetchAuthenticatedUser(){
        store.dispatch('fetchAuthenticatedUser');
    },


    // Unset the user from our Vuex store.
    _unsetAuthenticatedUser(){
        store.commit('setUser', '');
    },


    // Turn client back into a Guest.
    _revertToGuest(){
        this._removeAuthToken();
        this._removeRefreshToken();
        this._unsetHeaders();
        this._unsetAuthenticatedUser();
        router.push('/login');
    },


    // Setup everything up on page-load. This only gets fired once
    // and is called within bootstrap.js
    setup() {

        // Set Interceptor. This must occur first for all subsequent
        // requests to go through the interceptor.
        this._pushResourceInterceptor();

        // User is logged in on page load.
        if (this._getAuthToken()) {
            this._setHeaders(this._getAuthToken());     // Set our request headers
            this._fetchAuthenticatedUser();             // fetch User
        }
    },


    // Check if there is an authenticated User.
    check() {
        return !!this._getAuthToken();
    },


    // Login the user client-side. The response is what we get back
    // from either '/login' or '/register'.
    login(response){

        let authToken = 'Bearer ' + response.token;
        this._storeAuthToken(authToken);
        let refreshToken = response.refresh_token;
        this._storeRefreshToken(refreshToken);

        this._setHeaders(authToken);
        this._fetchAuthenticatedUser();

        this._goRedirectPath();
    },


    // Logout authenticated user
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
