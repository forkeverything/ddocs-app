import Vue from 'vue/dist/vue';
import Vuex from 'vuex';

// Modules
import project from './modules/project';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        authenticatedUser: ''
    },
    mutations: {
        setUser(state, payload) {
            state.authenticatedUser = payload;
        }
    },
    actions: {
        fetchAuthenticatedUser(context) {
            Vue.http.get('/api/auth_user').then((res) => {
                // Got user, token was good.
                context.commit('setUser', res.json());
            }, (res) => {
                // Before we get here our interceptor will catch a bad token and
                // redirect to login
                console.log("Couldn't get authenticated user");
            });
        }
    },
    modules: {
        project
    }
});
