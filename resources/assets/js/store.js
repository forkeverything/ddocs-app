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

            $.get('/auth/user')
                .done((user) => {
                    context.commit('setUser', user);
                })
                .fail(() => {
                    AuthCookie.remove();
                });
        }
    }
};