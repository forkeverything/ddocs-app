module.exports = {

    // The path to redirect  to after login/register in case we've got there
    // from needing to refresh token.
    redirectPath: '',

    // Check if the response we got includes a new token and save it out
    // if there is.
    refreshToken(response) {
        let newToken = response.headers.authorization;
        if(newToken) {
            this.storeCookie(newToken);
        }
    },

    // If our token isnt' good, we'll have the user login and get a new one.
    // We should also save where the User was so we can redirect back.
    checkResponseTokenInvalid(response) {
        if(response.status === 401 && (response.json().error === 'token_invalid' || response.json().error === 'token_expired')) {
            this.redirectPath = router.currentRoute.fullPath;
            router.push('/login');
        }
    },

    // Save a token in a cookie so we can read it for each request.
    storeCookie(token){
        // store a cookie so it'll be read on refresh
        Cookies.set('ddocs_auth', token);
        this.setHeaders(token);
    },

    // Add token to our headers. Since we know we're authenticated here. We'll
    // tell Vuex to fetch our user here too.
    setHeaders(value) {
        // set our request header
        Vue.http.headers.common['Authorization'] = value;
        $.ajaxSetup({
            headers: {
                'Authorization': value
            }
        });
        store.dispatch('fetchAuthenticatedUser');
    },

    // Read our cookie
    getCookie() {
        return Cookies.get('ddocs_auth');
    },

    // Delete our cookie
    removeCookie(){
        return Cookies.remove('ddocs_auth');
    },

    // If we've logged-in/registered off a redirect, then we'll send
    // the user back to where they came from here.
    goRedirectPath(){
        if(this.redirectPath) {
            router.push(this.redirectPath);
            this.redirectPath = '';
        } else {
            router.push('/');
        }
    }
};
