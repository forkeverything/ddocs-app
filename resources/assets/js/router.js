import * as Authenticator from "./authenticator";
import * as RequestsMonitor from "./requests-monitor";
module.exports = (function () {

    const VueRouter = require('vue-router');
    const router = new VueRouter({
        mode: 'history',
        routes: require('./routes.js')
    });

    // Global guards to check for meta properties on our routes
    router.beforeEach((to, from, next) => {

        // Clear phantom modal backdrops
        $('.modal-backdrop').remove();

        // Clear all pending fetch requests
        RequestsMonitor.flushQueue('fetch');

        // requiresAuth
        if (to.matched.some(record => record.meta.requiresAuth)) {
            if (! Authenticator.check()) {
                Authenticator.setRedirectPath(to.path);
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
            if (Authenticator.check()) {
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