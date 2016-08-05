Vue.component('checklist-single', fetchesFromEloquentRepository.extend({
    name: 'checklistSingle',
    el: function () {
        return '#checklist-single'
    },
    data: function () {
        return {
            hasFilters: true,
            filterOptions: [
                {
                    value: 'required',
                    label: 'Requirement'
                },
                {
                    value: 'version',
                    label: 'Version'
                },
                {
                    value: 'due',
                    label: 'Due Date'
                },
                {
                    value: 'status',
                    label: 'Status'
                }
            ]
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