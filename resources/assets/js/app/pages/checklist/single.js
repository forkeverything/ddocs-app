Vue.component('checklist-single', fetchesFromEloquentRepository.extend({
    name: 'checklistSingle',
    el: function () {
        return '#checklist-single'
    },
    data: function () {
        return {
            hasFilters: false
        };
    },
    props: ['checklist-hash'],
    computed: {
        requestUrl: function() {
            return '/checklist/' + this.checklistHash + '/files';
        },
        files: function() {
            return _.omit(this.response.data, 'query_parameters');
        }
    },
    methods: {},
    events: {},
    ready: function () {

    }
}));