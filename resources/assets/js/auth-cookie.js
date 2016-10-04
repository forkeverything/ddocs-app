module.exports = {
    store(token){
        // store a cookie so it'll be read on refresh
        Cookies.set('ddocs_auth', token);
        // set our request header
        Vue.http.headers.common['Authorization'] = 'Bearer ' + token;
        $.ajaxSetup({
            headers: {
                'Authorization': 'Bearer ' + token
            }
        });

    },
    get() {
        return Cookies.get('ddocs_auth');
    },
    remove(){
        return Cookies.remove('ddocs_auth');
    }
};
