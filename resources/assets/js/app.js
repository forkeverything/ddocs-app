
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

// Utilities
Vue.component('form-errors', require('./components/utilities/FormErrors.vue'));
Vue.component('tagger', require('./components/utilities/Tagger.vue'));
Vue.component('toggle-switch', require('./components/utilities/ToggleSwitch.vue'));
Vue.component('smart-date', require('./components/utilities/SmartDate.vue'));
Vue.component('cube-loader', require('./components/utilities/CubeLoader.vue'));

// Checklist
Vue.component('checklist-collection', require('./components/checklist/Collection.vue'));
Vue.component('checklist-single', require('./components/checklist/Single.vue'));
Vue.component('checklist-recipients', require('./components/checklist/single/Recipients.vue'));
Vue.component('mobile-file-menu', require('./components/checklist/single/MobileFileMenu.vue'));
Vue.component('summary-view', require('./components/checklist/single/SummaryView.vue'));
Vue.component('file-view', require('./components/checklist/single/FileView.vue'));
Vue.component('selected-file-date', require('./components/checklist/single/DateSelectedFile.vue'));
Vue.component('selected-file-weighting', require('./components/checklist/single/WeightingSelectedFile.vue'));
Vue.component('file-request-notes', require('./components/checklist/single/FileRequestNotes.vue'));
Vue.component('single-note', require('./components/checklist/single/SingleNote.vue'));
Vue.component('checklist-maker', require('./components/checklist/Maker.vue'));
Vue.component('maker-files', require('./components/checklist/maker/SelectFiles.vue'));
Vue.component('checklist-offer-join', require('./components/checklist/single/Offer.vue'));

// File Requests
Vue.component('file-reject-modal', require('./components/file/RejectModal.vue'));
Vue.component('file-delete-modal', require('./components/file/DeleteModal.vue'));
Vue.component('file-filters', require('./components/file/Filters.vue'));
Vue.component('file-active-filters', require('./components/file/ActiveFilters.vue'));
Vue.component('file-uploader', require('./components/file/Uploader.vue'));
Vue.component('file-date-input', require('./components/file/DateInput.vue'));

// Projects
Vue.component('project-board', require('./components/projects/ProjectBoard.vue'));
Vue.component('new-board-item', require('./components/projects/NewBoardItem.vue'));
Vue.component('single-board-item', require('./components/projects/SingleBoardItem.vue'));

Vue.component('project-items', require('./components/projects/ProjectItems.vue'));
Vue.component('new-project-file', require('./components/projects/NewProjectFile.vue'));
Vue.component('new-project-item', require('./components/projects/NewProjectItem.vue'));
Vue.component('project-file', require('./components/projects/ProjectFile.vue'));
Vue.component('project-category', require('./components/projects/ProjectCategory.vue'));

Vue.component('board-item-name', require('./components/projects/BoardItemName.vue'));
Vue.component('delete-item-modal', require('./components/projects/DeleteItemModal.vue'));

// Vue.component('file-renamer', require('./components/file/Renamer.vue'));

// Account
Vue.component('add-credit-card', require('./components/account/AddCreditCard.vue'));

// Pagination
Vue.component('per-page-picker', require('./components/pagination/PerPagePicker.vue'));
Vue.component('paginator', require('./components/pagination/Paginator.vue'));

// Filters
Vue.component('integer-range-field', require('./components/filters/IntegerRange.vue'));
Vue.component('date-range-field', require('./components/filters/DateRange.vue'));

window.vueGlobalEventBus = new Vue();

const app = new Vue({
    el: 'body'
});
