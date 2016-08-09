Vue.component('checklist-all', fetchesFromEloquentRepository.extend({
    name: 'checklistAll',
    el: function () {
        return '#checklist-all'
    },
    data: function () {
        return {
            ajaxReady: true,
            hasFilters: false,
            requestUrl: "checklist/get",
            checklists: []
        };
    },
    props: [],
    computed: {},
    methods: {
        viewChecklist: function(checklist) {
            location.href = "/checklist/" + checklist.hash
        }
    },
    events: {},
    ready: function () {
        var self = this;
        this.$watch('response', function (response) {
            self.checklists = $.map(_.omit(response.data, 'query_parameters'), function (checklist, index) {
                return checklist;
            });
        });
    }
}));