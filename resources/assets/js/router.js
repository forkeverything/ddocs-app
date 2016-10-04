module.exports = (function () {


    const VueRouter = require('vue-router');
    const router = new VueRouter({
        mode: 'history',
        routes: require('./routes.js')
    });


    // Global guards to check for meta properties on our routes
    router.beforeEach((to, from, next) => {

        // requiresAuth
        if (to.matched.some(record => record.meta.requiresAuth)) {
            if (!AuthCookie.get()) {
                next({
                    path: '/login',
                    query: {redirect: to.fullPath}
                })
            } else {
                next()
            }
        } else {
            next()
        }

        // guestOnly
        if (to.matched.some(record => record.meta.guestOnly)) {
            if (AuthCookie.get()) {
                next('/');
            } else {
                next();
            }
        } else {
            next();
        }
    });

    return router;
})();