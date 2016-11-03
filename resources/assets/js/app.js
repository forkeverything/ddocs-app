
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

// System (App-wide)
Vue.component('navbar', require('./components/system/Navbar.vue'));

// Auth
Vue.component('login', require('./components/auth/Login.vue'));

// Utilities
Vue.component('form-errors', require('./components/utilities/FormErrors.vue'));
Vue.component('tagger', require('./components/utilities/Tagger.vue'));
Vue.component('tag-input', require('./components/utilities/TagInput.vue'));
Vue.component('toggle-switch', require('./components/utilities/ToggleSwitch.vue'));
Vue.component('smart-date', require('./components/utilities/SmartDate.vue'));
Vue.component('rectangle-loader', require('./components/utilities/RectangleLoader.vue'));
Vue.component('editable-text-field', require('./components/utilities/EditableTextField.vue'));
Vue.component('editable-text-area', require('./components/utilities/EditableTextArea.vue'));
Vue.component('editable-number-field', require('./components/utilities/EditableNumberField.vue'));
Vue.component('date-picker', require('./components/utilities/DatePicker.vue'));

// Checklist
Vue.component('checklist-collection', require('./components/checklist/Collection.vue'));
Vue.component('stats-recipients-dropdown', require('./components/checklist/collection/RecipientsDropdown.vue'));
Vue.component('checklist-single', require('./components/checklist/Single.vue'));
Vue.component('checklist-recipients', require('./components/checklist/single/Recipients.vue'));
Vue.component('recipients-editor', require('./components/checklist/single/RecipientsEditor.vue'));
Vue.component('mobile-file-menu', require('./components/checklist/single/MobileFileMenu.vue'));
Vue.component('summary-view', require('./components/checklist/single/SummaryView.vue'));
Vue.component('file-view', require('./components/checklist/single/FileView.vue'));
Vue.component('file-request-notes', require('./components/checklist/single/FileRequestNotes.vue'));
Vue.component('single-note', require('./components/checklist/single/SingleNote.vue'));
Vue.component('add-file-request', require('./components/checklist/single/AddFileRequest.vue'));
Vue.component('checklist-maker', require('./components/checklist/Maker.vue'));
Vue.component('files-selecter', require('./components/checklist/maker/FilesSelecter.vue'));
Vue.component('checklist-offer-join', require('./components/checklist/single/Offer.vue'));

// File Requests
Vue.component('fr-history-modal', require('./components/file-requests/HistoryModal.vue'));
Vue.component('fr-reject-modal', require('./components/file-requests/RejectModal.vue'));
Vue.component('fr-delete-modal', require('./components/file-requests/DeleteModal.vue'));
Vue.component('fr-filters', require('./components/file-requests/Filters.vue'));
Vue.component('fr-active-filters', require('./components/file-requests/ActiveFilters.vue'));
Vue.component('fr-uploader', require('./components/file-requests/Uploader.vue'));
Vue.component('fr-due-date', require('./components/file-requests/DueDate.vue'));

// Projects
Vue.component('project-members', require('./components/projects/ProjectMembers.vue'));
Vue.component('project-folder', require('./components/projects/ProjectFolder.vue'));
Vue.component('project-file', require('./components/projects/ProjectFile.vue'));
    // Members
    Vue.component('member-avatar', require('./components/projects/members/Avatar.vue'));
    Vue.component('project-single-member', require('./components/projects/members/SingleProjectMember.vue'));
    Vue.component('member-actions', require('./components/projects/members/Actions.vue'));
    // Folders
    Vue.component('form-add-project-folder', require('./components/projects/folders/Add.vue'));
    // Files
    Vue.component('add-project-file', require('./components/projects/files/Add.vue'));
    Vue.component('project-file-modal', require('./components/projects/files/Modal.vue'));
    Vue.component('pf-status-circle', require('./components/projects/files/StatusCircle.vue'));
        // Modal
        Vue.component('pfm-project-view', require('./components/projects/files/modal/ProjectView.vue'));
        Vue.component('pfm-request-view', require('./components/projects/files/modal/RequestView.vue'));
        Vue.component('attach-fr-dropdown', require('./components/projects/files/modal/AttachFileRequestDropdown.vue'));
        Vue.component('pf-members', require('./components/projects/files/modal/FileMembers.vue'));
        Vue.component('pf-single-member', require('./components/projects/members/SingleFileMember.vue'));
        Vue.component('pf-uploader', require('./components/projects/files/modal/Uploader.vue'));
        Vue.component('pf-downloader', require('./components/projects/files/modal/Downloader.vue'));

// Comments
Vue.component('comments-thread', require('./components/comments/CommentsThread.vue'));
Vue.component('new-comment-field', require('./components/comments/NewCommentField.vue'));

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
    el: '#app',
    router,
    store
});
