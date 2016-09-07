window._ = require('lodash');

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

window.Vue = require('vue');
require('vue-resource');

/**
 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
 * the outgoing requests issued by this application. The CSRF middleware
 * included with Laravel will automatically verify the header's value.
 */

Vue.http.interceptors.push((request, next) => {
    request.headers['X-CSRF-TOKEN'] = Laravel.csrfToken;

    next();
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
        'X-CSRF-TOKEN': Laravel.csrfToken,
        'Authorization': 'Bearer ' + localStorage.getItem('token')
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
let ResizeSensor = require('../../../node_modules/css-element-queries/src/ResizeSensor');
$(window).on('load', () => {
    let element = document.getElementById('page-fixed-top');
    if(! element){
        adjustScrollDivPosition(0);
        return;
    }
    adjustScrollDivPosition(element.clientHeight);
    let sensor = new ResizeSensor(element, function () {
        adjustScrollDivPosition(element.clientHeight);
    });
});
function adjustScrollDivPosition(fixedDivHeight) {
    let navHeight = 50;
    $('#page-scroll-content').css('top', fixedDivHeight + navHeight);
    revealContent();
}
function revealContent() {
    $('#page-scroll-content').css('opacity', 1);
    $('#page-fixed-top').css('opacity', 1);
}



