
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

Vue.component('form-errors', require('./components/FormErrors.vue'));

Vue.component('my-checklists', require('./components/checklist/MyChecklists.vue'));
Vue.component('checklist-maker', require('./components/checklist/Maker.vue'));
Vue.component('checklist-notifications-control', require('./components/checklist/NotificationsControl.vue'));
Vue.component('checklist-file-requests', require('./components/checklist/FileRequests.vue'));

Vue.component('add-credit-card', require('./components/account/AddCreditCard.vue'));

Vue.component('per-page-picker', require('./components/pagination/PerPagePicker.vue'));
Vue.component('paginator', require('./components/pagination/Paginator.vue'));

Vue.component('integer-range-field', require('./components/filters/IntegerRange.vue'));
Vue.component('date-range-field', require('./components/filters/DateRange.vue'));

window.vueGlobalEventBus = new Vue();

const app = new Vue({
    el: 'body'
});
