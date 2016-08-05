Vue.component('per-page-picker', {
    name: 'itemsPerPagePicker',
    template: '<div class="per-page-picker">' +
    // '<select-picker :name.sync="newItemsPerPage" :options.sync="itemsPerPageOptions" :function="changeItemsPerPage"></select-picker>' +
    '<select class="form-control" @change="changeItemsPerPage" v-model="newItemsPerPage">' +
    '<option v-for="option in itemsPerPageOptions">{{ option }}</option>' +
    '</select>' +
    '<span>per page</span>' +
    '</div>',
    el: function () {
        return ''
    },
    data: function () {
        return {
            newItemsPerPage: '',
            itemsPerPageOptions: [
                20, 50, 100
            ]
        };
    },
    props: ['response', 'reqFunction'],
    computed: {
        currentItemsPerPage: function () {
            return this.response.per_page;
        }
    },
    methods: {
        changeItemsPerPage: function () {
            var self = this;
            if (self.newItemsPerPage !== self.currentItemsPerPage) {
                self.reqFunction(updateQueryString({
                    page: 1, // Reset to page 1
                    per_page: self.newItemsPerPage // Update items per page
                }));
            }
        }
    },
    ready: function () {
        this.$watch('currentItemsPerPage', function (numItems) {
            this.newItemsPerPage = numItems;
        });
    }
});