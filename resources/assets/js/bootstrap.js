window._ = require('lodash');

/**
 * Cookies
 */
window.Cookies = require('js-cookie');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.$ = window.jQuery = require('jquery');
require('bootstrap-sass');

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

window.Vue = require('vue/dist/vue.js');
require('vue-resource');

/**
 * Use Vuex
 */

import Vuex from 'vuex';
Vue.use(Vuex);
window.store = new Vuex.Store(require('./store.js'));

/**
 * Vue Router
 */

window.router = require('./router.js');

/**
 * Auth Cookie Helper. Makes handling the authentication cookie
 * a little more semantic.
 */
window.auth = require('./auth.js');
if(auth.getCookie()) auth.setHeaders(auth.getCookie());

/**
 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
 * the outgoing requests issued by this application. The CSRF middleware
 * included with Laravel will automatically verify the header's value.
 */

Vue.http.interceptors.push((request, next) => {

    request.headers['X-CSRF-TOKEN'] = Laravel.csrfToken;

    // As a precaution we'll set our token header here too. it
    // should have already been set above.
    request.headers['Authorization'] = auth.getCookie();

    // Intercept the response too so we can control our JWT
    next((response) => {
        auth.refreshToken(response);
        auth.checkResponseTokenInvalid(response);
        return response;
    });
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from "laravel-echo"

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });

// Setup jQuery AJAX to use CSRF Token too.
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': Laravel.csrfToken
    }
});


// Initialize autosize() for textareas.
let autosize = require('autosize');
$(document).ready(() => autosize($('textarea')));

// Define moment.js
window.moment = require('moment');

// tooltip.js (boostrap)
$(document).ready(() => $('[data-toggle="tooltip"]').tooltip());

// Include jQuery UI Datepicker
require('jquery-ui/ui/widgets/datepicker');

// Vue Filters
require('./filters.js');

// Vue Directives
require('./directives.js');

// Vue Animations
require('./animations.js');

// JS Helpers (utility functions)
require('./helpers.js');

// Page Fixed/Scroll Height adjustments
window.ResizeSensor = require('css-element-queries/src/ResizeSensor');

// Scroll monitor
window.scrollMonitor = require("scrollmonitor");

// Dragula
window.dragula = require('dragula');
window.autoScroll = require('dom-autoscroller');
