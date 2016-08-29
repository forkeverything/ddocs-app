<template>
    <div id="my-checklists">
        <form id="form-all-checklists-search" @submit.prevent="searchTerm">
            <div class="form-group">
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

        <div class="table-responsive" v-show="checklists.length > 0">
            <!-- Checklists Table -->
            <table class="table table-standard table-hover">
                <thead>
                <tr>
                    <th class="sortable"
                        :class="{
                            'current_asc': params.sort === 'name' && params.order === 'asc',
                            'current_desc': params.sort === 'name' && params.order === 'desc',
                            }"
                        @click="changeSort('name')"
                    >
                        Name
                    </th>
                    <th class="sortable"
                        :class="{
                            'current_asc': params.sort === 'recipient' && params.order === 'asc',
                            'current_desc': params.sort === 'recipient' && params.order === 'desc',
                            }"
                        @click="changeSort('recipient')"
                    >
                        Recipient
                    </th>
                    <th class="sortable"
                        :class="{
                            'current_asc': params.sort === 'created_at' && params.order === 'asc',
                            'current_desc': params.sort === 'created_at' && params.order === 'desc',
                            }"
                        @click="changeSort('created_at')"
                    >
                        Created
                    </th>
                    <th>Progress</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="checklist in checklists">
                    <tr>
                        <td>
                            <a :href="'/checklist/' + checklist.hash ">{{ checklist.name }}</a>
                        </td>
                        <td>
                            {{ checklist.recipient }}
                        </td>
                        <td>
                            {{ checklist.created_at | easyDate }}
                        </td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" :aria-valuenow="checklist.progress" aria-valuemin="0" aria-valuemax="100" :style="'width: ' + checklist.progress + '%' + ';min-width: 2em;'">
                                    {{ checklist.progress }}% Received
                                </div>
                            </div>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>

        <h3 v-else class="text-center text-muted">Sorry we couldn't find any lists for you. <a href="/checklist/make">Make</a> a new one or
            <a href="#" @click.prevent="clearSearch">clear</a> your search to see more.</h3>

        <div class="page-controls">
        <per-page-picker :response="response" :req-function="fetchResults"></per-page-picker>
        <paginator :response="response" :req-function="fetchResults"></paginator>
        </div>
    </div>
</template>
<script>
    const fetchesFromEloquentRepository = require('../../mixins/fetchesFromEloquentRepository');

    export default {
        name: 'myChecklists',
        data: function () {
            return {
                ajaxReady: true,
                hasFilters: false,
                requestUrl: "checklist/get",
                checklists: []
            }
        },
        methods: {},
        mixins: [fetchesFromEloquentRepository],
        ready: function () {
            var self = this;
            this.$watch('response', function (response) {
                self.checklists = $.map(_.omit(response.data, 'query_parameters'), function (checklist, index) {
                    return checklist;
                });
            });
        }
    };
</script>