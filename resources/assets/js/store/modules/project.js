/**
 * Vuex - Project Module
 */

/**
 * Aliasing our method names - getters, mutations and actions are
 * all under global namespace so we should prevent clashes.
 *
 * @type {{SET: string, INSERT_FOLDER: string, UPDATE_FOLDER: string, REMOVE_FOLDER: string, INSERT_FILE: string, UPDATE_FILE: string, REMOVE_FILE: string}}
 */
const types = {
    SET: 'project/SET',
    INSERT_FOLDER: 'project/INSERT_FOLDER',
    UPDATE_FOLDER: 'project/UPDATE_FOLDER',
    REMOVE_FOLDER: 'project/REMOVE_FOLDER',
    INSERT_FILE: 'project/INSERT_FILE',
    UPDATE_FILE: 'project/UPDATE_FILE',
    REMOVE_FILE: 'project/REMOVE_FILE',
    SAVE_CHANGES: 'project/SAVE_CHANGES',
    PREPARE_REQUEST: 'project/PREPARE_REQUEST',
    RESET_REQUEST: 'project/RESET_REQUEST',
    SET_TIMEOUT: 'project/SET_TIMEOUT',
    CLEAR_TIMEOUT: 'project/CLEAR_TIMEOUT',
    SEND_REQUEST: 'project/SEND_REQUEST',
    DEFINE_MEMBER_MANAGER: 'project/DEFINE_MEMBER_MANAGER',
    REMOVE_MEMBER: 'project/REMOVE_MEMBER'
};

/**
 * Project Module Local State
 *
 * @type {{project: string}}
 */
const state = {
    data: '',
    // Update Project items request
    request: {
        project: {},
        folders: {},
        files: {}
    },
    // setTimeout object to fire update request
    timeout: ''
};

/**
 * Project Mutations
 *
 * @type {{}}
 */
const mutations = {
    [types.SET](state, payload) {
        state.data = payload;
    },
    [types.INSERT_FOLDER](state, payload) {
        state.data.folders.splice(payload.index, 0, payload.folder);
    },
    [types.UPDATE_FOLDER](state, payload) {
        for (let prop in payload.folder) {
            if (payload.folder.hasOwnProperty(prop)) {
                state.data.folders[payload.index][prop] = payload.folder[prop];
            }
        }
    },
    [types.REMOVE_FOLDER](state, index) {
        state.data.folders.splice(index, 1);
    },
    [types.INSERT_FILE](state, payload) {
        state.data.folders[payload.folderIndex].files.splice(payload.fileIndex, 0, payload.file);
    },
    [types.UPDATE_FILE](state, payload) {
        for (let prop in payload.file) {
            if (payload.file.hasOwnProperty(prop)) {
                state.data.folders[payload.folderIndex].files[payload.fileIndex][prop] = payload.file[prop];
            }
        }
    },
    [types.REMOVE_FILE](state, payload) {
        state.data.folders[payload.folderIndex].files.splice(payload.fileIndex, 1);
    },

    [types.SAVE_CHANGES](state, payload) {

        function updateProperties(data, updatedProperties) {
            for(let prop in updatedProperties) {
                if(updatedProperties.hasOwnProperty(prop)) {
                    if(! data) data = {};
                    data[prop] = updatedProperties[prop];
                }
            }
            return data;
        }

        let type = payload.type;
        if (type === 'project') {
            state.request.project = updateProperties(state.request.project, payload.model);
        } else {
            state.request[type][payload.model.id] = updateProperties(state.request[type][payload.model.id], payload.model);
        }
        store.dispatch(types.PREPARE_REQUEST);
    },
    [types.RESET_REQUEST](state) {
        state.request = {
            project: {},
            folders: {},
            files: {}
        };
    },
    [types.SET_TIMEOUT](state, payload) {
        state.timeout = setTimeout(payload.func, payload.delay);
    },
    [types.CLEAR_TIMEOUT](state) {
        clearTimeout(state.timeout);
    },
    [types.DEFINE_MEMBER_MANAGER](state, payload) {
        let targetMember = _.find(state.data.members, (member) => {
            return member.id === payload.id;
        });
        targetMember.pivot.manager = payload.manager;
    },
    [types.REMOVE_MEMBER](state,payload){
        let index = _.indexOf(state.data.members, _.find(state.data.members, (member) => member.id === payload.id));
        state.data.members.splice(index, 1);
    }
};

/**
 * Project Module Actions
 *
 * @type {{}}
 */
const actions = {
    [types.PREPARE_REQUEST](context) {
        // Func that gets called on timeout
        let fire = function() {
            store.dispatch(types.SEND_REQUEST);
        };
        context.commit(types.CLEAR_TIMEOUT);
        context.commit(types.SET_TIMEOUT, {
            func: fire,
            delay: 1500     // Time since last update before sending request
        });
    },
    [types.SEND_REQUEST](context) {
        Vue.http.put(`/api/projects/${ context.state.data.id }`, context.state.request, {
            before(xhr) {
                RequestsMonitor.pushOntoQueue(xhr);
            }
        }).then((res) => {
            console.log('updated project items');
            context.commit(types.RESET_REQUEST);
        }, (res) => {
            console.log('failed updating project items');
        });
    }
};

export default {
    state,
    mutations,
    actions
}