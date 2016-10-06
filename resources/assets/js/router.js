module.exports = (function () {

    const VueRouter = require('vue-router');
    const router = new VueRouter({
        mode: 'history',
        routes: require('./routes.js')
    });

    // Global guards to check for meta properties on our routes
    router.beforeEach((to, from, next) => {

        // Clear all pending fetch requests 
        RequestsMonitor.flushQueue('fetch');

        // requiresAuth
        if (to.matched.some(record => record.meta.requiresAuth)) {
            if (! auth.getCookie()) {
                next({
                    path: '/login'
                })
            } else {
                next()
            }
        } else {
            next()
        }

        // guestOnly
        if (to.matched.some(record => record.meta.guestOnly)) {
            if (auth.getCookie()) {
                next({
                    path: '/'
                });
            } else {
                next();
            }
        } else {
            next();
        }
    });

    return router;
})();