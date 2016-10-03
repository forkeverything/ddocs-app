module.exports = [
    {
        path: '/', redirect: '/checklists'
    },
    {
        path: '/checklists', component: require('./components/checklist/Collection.vue')
    },
    {
        path: '/checklists/make', component: require('./components/checklist/Maker.vue')
    },
    {
        path: '/checklists/:checklist_hash/:checklist_name?', component: require('./components/checklist/Single.vue')
    },
    {
        path: '/projects', component: { template: '<h1>All Projects</h1>' }
    },
    {
        path: '/projects/:project_id', component: require('./components/checklist/Single.vue')
    },
    {
        path: '/account', component: { template: '<h1>Account Overview</h1>' }
    }
];

