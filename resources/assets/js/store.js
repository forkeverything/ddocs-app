module.exports = {
    state: {
        authenticatedUser: ''
    },
    mutations: {
        setUser(state, user) {
            state.authenticatedUser = user;
        }
    },
    actions: {
        fetchAuthenticatedUser(context) {
            Vue.http.get('/api/user').then((res) => {
                // Got user, token was good.
                context.commit('setUser', res.json());
            }, (res) => {
                // Before we get here our interceptor will catch a bad token and
                // redirect to login
                console.log("Couldn't get authenticated user");
            });
        }
    }
};