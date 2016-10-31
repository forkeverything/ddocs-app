module.exports = [
    {
        path: '/',
        redirect: '/checklists'
    },
    {
        path: '/login',
        component: require('./components/auth/Login.vue'),
        meta: { guestOnly: true }
    },
    {
        path: '/register',
        component: require('./components/auth/Register.vue'),
        meta: { guestOnly: true }
    },
    {
        path: '/password/reset',
        component: { template: '<h1>Show Link Request form</h1>' },
        meta: { guestOnly: true }
    },
    {
        path: '/password/reset/:token',
        component: { template: '<h1>show reset form</h1>' },
        meta: { guestOnly: true }
    },
    {
        path: '/checklists',
        component: require('./components/checklist/Collection.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/checklists/make',
        component: require('./components/checklist/Maker.vue'),
        meta: { requiresAuth: true }
    },
    {
        name: 'checklistSingle',
        path: '/c/:checklist_hash/:checklist_name?',
        component: require('./components/checklist/Single.vue')
    },
    {
        path: '/projects',
        component: require('./components/projects/ProjectsList.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/projects/:project_id',
        component: require('./components/projects/ProjectBoard.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/projects/:project_id/join',
        component: require('./components/projects/JoinProject.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/account',
        component: { template: '<h1>Account Overview</h1>' },
        meta: { requiresAuth: true }
    },
    {
        path: '*',
        redirect: '/'
    },
];

