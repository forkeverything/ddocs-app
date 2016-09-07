<template>
    <div id="checklist-file-requests">
        <form id="form-checklist-search" @submit.prevent="searchTerm">
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">Filters <span class="caret"></span>
                    </button>

                    <file-filters :filter-options="filterOptions" :min-filter-value.sync="minFilterValue"
                                  :max-filter-value.sync="maxFilterValue" :filter.sync="filter"
                                  :filter-value.sync="filterValue" :add-filter="addFilter"></file-filters>

                </div>
                <input class="form-control input-search"
                       type="text"
                       placeholder="Search..."
                       @keyup="searchTerm"
                       v-model="params.search"
                       :class="{
                                    'active': params.search && params.search.length > 0
                               }"
                >
            </div>
        </form>

        <file-active-filters :params.sync="params" :remove-filter="removeFilter"></file-active-filters>

        <p class="text-muted small text-right">* maximum file size 1GB</p>
        <div id="files-collection">
            <ul id="files-header" class="list-unstyled list-inline">
                <li class="column col-file header-column"
                    :class="{
                            'current_asc': params.sort === 'status' && params.order === 'asc',
                            'current_desc': params.sort === 'status' && params.order === 'desc',
                            }"
                    @click="changeSort('status')"
                >
                    <i class="fa fa-file-o"></i>
                </li>
                <li class="column col-name header-column"
                    :class="{
                            'current_asc': params.sort === 'name' && params.order === 'asc',
                            'current_desc': params.sort === 'name' && params.order === 'desc',
                            }"
                    @click="changeSort('name')"
                >
                    File
                </li>
                <li class="column col-due header-column"
                    :class="{
                            'current_asc': params.sort === 'due' && params.order === 'asc',
                            'current_desc': params.sort === 'due' && params.order === 'desc',
                            }"
                    @click="changeSort('due')"
                >
                    Due
                </li>
                <li class="column col-upload header-column">
                    <!-- empty spacer column-->
                </li>
            </ul>
            <ul id="files-list" class="list-unstyled">
                <li class="single-file" v-for="file in files">
                    <div class="column col-file content-column file-status" :class="file.status">
                        <i class="fa fa-file-o"></i>
                    </div>
                    <div class="column col-name content-column">
                        {{ file.name }}
                    </div>
                    <div class="column col-due content-column">
                        <span class="date" v-if="file.due">{{ file.due | date }}</span>
                        <span v-else>--</span>
                    </div>
                    <div class="column col-upload content-column">
                        <file-uploader :file-request.sync="file" :with-text="true"></file-uploader>
                    </div>
                </li>
            </ul>
        </div>
        <div class="page-controls">
            <per-page-picker :response="response" :req-function="fetchResults"></per-page-picker>
            <paginator :response="response" :req-function="fetchResults"></paginator>
        </div>
    </div>
</template>
<script>
    const fetchesFromEloquentRepository = require('../../mixins/fetchesFromEloquentRepository');

    export default {
        name: 'checklistFileRequests',
        data: function () {
            return {
                ajaxReady: true,
                hasFilters: true,
                filterOptions: [
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
                ],
                files: [],
                numReceived: ''
            }
        },
        props: ['checklist-hash', 'is-owner', 'aws-url'],
        computed: {
            requestUrl: function () {
                return '/checklist/' + this.checklistHash + '/files';
            },
            receivedFilesPercentage: function () {
                if (!this.response.total) return 0;
                return (100 * this.numReceived / this.response.total).toFixed(2);
            }
        },
        mixins: [fetchesFromEloquentRepository],
        methods: {},
        ready: function () {

            var self = this;

            self.$watch('response', (response) => {
                self.files = $.map(_.omit(response.data, 'query_parameters'), (file, index) => {
                    return file;
                });
                self.numReceived = self.params.num_received_files;
            });
        }
    }
</script>