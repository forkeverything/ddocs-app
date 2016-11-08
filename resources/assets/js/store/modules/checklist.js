/**
 * Vuex - Checklist Module
 */

const types = {
    SET: 'checklist/SET',
    UPDATE_RECIPIENTS: 'checklist/UPDATE_RECIPIENTS'
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
    [types.UPDATE_RECIPIENTS](state, payload) {
        state.data.recipients = payload;
    }
};

/**
 * Checklist Actions
 *
 * @type {{}}
 */
const actions = {};

export default {
    state,
    mutations,
    actions
}