import Vue from 'vue/dist/vue';
import Vuex from 'vuex';

// Modules
import project from './modules/project';
import checklist from './modules/checklist';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        showSidebar: false,
        navTitle: '',
        authenticatedUser: ''
    },
    mutations: {
        setUser(state, payload) {
            state.authenticatedUser = payload;
        },
        setTitle(state, payload) {
            state.navTitle = payload;
        },
        toggleSidebar(state, payload) {
            state.showSidebar = ! state.showSidebar;
        },
        updateUser(state, payload) {
            for (let prop in payload) {
                if (payload.hasOwnProperty(prop)) state.authenticatedUser[prop] = payload[prop];
            }
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
        },
        saveUserChanges(context, payload) {
            Vue.http.put('/api/auth_user', payload, {
                before(xhr) {
                    RequestsMonitor.pushOntoQueue(xhr);
                }
            }).then((res) => {
                console.log('updated user');
            }, (res) => {
                console.log('failed updating user');
            });
        }
    },
    modules: {
        project,
        checklist
    }
});
