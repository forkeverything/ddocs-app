<template>
    <div class="per-page-picker">
            <select class="form-control" @change="changeItemsPerPage" v-model="newItemsPerPage" v-if="moreThanOnePage">
                <option v-for="option in itemsPerPageOptions">{{ option }}</option>
            </select>
            <span v-if="moreThanOnePage">per page</span>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                newItemsPerPage: '',
                itemsPerPageOptions: [
                    20, 50, 100
                ]
            }
        },
        props: ['response', 'reqFunction'],
        computed: {
            currentItemsPerPage: function () {
                return this.response.per_page;
            },
            moreThanOnePage: function() {
                return this.response.last_page > 1;
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
    }
</script>