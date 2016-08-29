
// Load the same dependencies as the app

/**
 * The reason for the different file landing.js - is so we can only register our landing components.
 * It will make our landing page load a little faster.
 */




require('./bootstrap');


Vue.component('form-errors', require('./components/FormErrors.vue'));
Vue.component('checklist-maker', require('./components/checklist/Maker.vue'));
Vue.component('registration-modal', require('./components/auth/RegistrationModal.vue'));

Vue.component('landing-email-form', require('./components/landing/EmailForm.vue'));
Vue.component('landing-list-maker', require('./components/landing/ListMaker.vue'));

window.vueGlobalEventBus = new Vue();

const app = new Vue({
    el: 'body'
});
