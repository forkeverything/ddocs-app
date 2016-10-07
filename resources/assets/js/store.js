module.exports = {
    state: {
        authenticatedUser: '',
        project: ''
    },
    mutations: {
        setUser(state, user) {
            state.authenticatedUser = user;
        },
        setProject(state, project) {
            state.project = project;
        },
        removeProjectFolder(state, index) {
            state.project.folders.splice(index, 1);
        },
        insertProjectFolder(state, args) {
            state.project.folders.splice(args.index, 0, args.folder);
        },
        updateProjectFolder(state, args) {
            for (let prop in args.folder) {
                if (args.folder.hasOwnProperty(prop)) {
                    state.project.folders[args.index][prop] = args.folder[prop];
                }
            }
        },
        removeProjectFile(state, args) {
            state.project.folders[args.folderIndex].files.splice(args.fileIndex, 1);
        },
        insertProjectFile(state, args) {
            state.project.folders[args.folderIndex].files.splice(args.fileIndex, 0, args.file);
        },
        updateProjectFile(state, args) {
            for (let prop in args.file) {
                if (args.file.hasOwnProperty(prop)) {
                    state.project.folders[args.folderIndex].files[args.fileIndex][prop] = args.file[prop];
                }
            }
        },
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