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

    checkResponseTokenInvalid(response) {
        if(response.status === 401 && (response.json().error === 'token_invalid' || response.json().error === 'token_expired')) {
            auth.removeCookie();
            // save where the user is currently at
            this.redirectPath = router.currentRoute.fullPath;
            router.push('/login');
        }
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
        $.ajaxSetup({
            headers: {
                'Authorization': token,
//              'X-CSRF-TOKEN': Laravel.csrfToken
            }
        });
        store.dispatch('fetchAuthenticatedUser');
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
            next((response) => {
                this.refreshToken(response);
                this.checkResponseTokenInvalid(response);
                return response;
            });
        });
    },

    /**
     * Setup everything up on page-load. This only gets fired once
     * and is called within bootstrap.js
     */

    setup(){
        // If we found a cookie (logged-in)
        if(this.getCookie()) {
            // Set our request headers
            this.setHeaders(this.getCookie());
        }
        this.pushResourceInterceptor();
    }
};
