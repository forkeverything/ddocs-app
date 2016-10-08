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
    SET: 'projects/SET',
    INSERT_FOLDER: 'projects/INSERT_FOLDER',
    UPDATE_FOLDER: 'projects/UPDATE_FOLDER',
    REMOVE_FOLDER: 'projects/REMOVE_FOLDER',
    INSERT_FILE: 'projects/INSERT_FILE',
    UPDATE_FILE: 'projects/UPDATE_FILE',
    REMOVE_FILE: 'projects/REMOVE_FILE'
};

/**
 * Project Module Local State
 *
 * @type {{project: string}}
 */
const state = {
    project: ''
};

/**
 * Project Mutations
 *
 * @type {{}}
 */
const mutations = {
    [types.SET](state, payload) {
        state.project = payload;
    },
    [types.INSERT_FOLDER](state, payload) {
        state.project.folders.splice(payload.index, 0, payload.folder);
    },
    [types.UPDATE_FOLDER](state, payload) {
        for (let prop in payload.folder) {
            if (payload.folder.hasOwnProperty(prop)) {
                state.project.folders[payload.index][prop] = payload.folder[prop];
            }
        }
    },
    [types.REMOVE_FOLDER](state, index) {
        state.project.folders.splice(index, 1);
    },
    [types.INSERT_FILE](state, payload) {
        state.project.folders[payload.folderIndex].files.splice(payload.fileIndex, 0, payload.file);
    },
    [types.UPDATE_FILE](state, payload) {
        for (let prop in payload.file) {
            if (payload.file.hasOwnProperty(prop)) {
                state.project.folders[payload.folderIndex].files[payload.fileIndex][prop] = payload.file[prop];
            }
        }
    },
    [types.REMOVE_FILE](state, payload) {
        state.project.folders[payload.folderIndex].files.splice(payload.fileIndex, 1);
    }
};

export default {
    state,
    mutations
}