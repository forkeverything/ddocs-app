/**
 * Vuex - Checklist Module
 */

const types = {
    SET: 'checklist/SET',
    UPDATE_DESCRIPTION: 'checklist/UPDATE_DESCRIPTION',
    UPDATE_RECIPIENTS: 'checklist/UPDATE_RECIPIENTS',
    SAVE_CHANGES: 'checklist/SAVE_CHANGES'
};

/**
 * Checklist Local State
 *
 * @type {{project: string}}
 */
const state = {
    data: ''
};

/**
 * Checklist Mutations
 *
 * @type {{}}
 */
const mutations = {
    [types.SET](state, payload) {
        state.data = payload;
    },
    [types.UPDATE_DESCRIPTION](state, payload) {
        state.data.description = payload;
    },
    [types.UPDATE_RECIPIENTS](state, payload) {
        state.data.recipients = payload;
    }
};

/**
 * Checklist Actions
 *
 * @type {{}}
 */
const actions = {
    [types.SAVE_CHANGES](context, payload) {
        Vue.http.put(`/api/c/${ context.state.data.hash }`, payload, {
            before(xhr) {
                RequestsMonitor.pushOntoQueue(xhr);
            }
        }).then((res) => {
            console.log('updated checklist');
        }, (res) => {
            console.log('failed updating checklist');
        });
    }
};

export default {
    state,
    mutations,
    actions
}