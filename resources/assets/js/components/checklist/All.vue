<template>

    <div id="checklist-all" class="container">
        <div id="page-fixed-top" class="container-no-gutter">
            <div class="header">
            <h3 class="page-title">
                <strong>Your Checklists</strong>
            </h3>
            <div>
                <a href="/checklist/make">
                    <button type="button" class="btn btn-primary">New Checklist</button>
                </a>
            </div>
            </div>
            <br>
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

            <div id="checklist-sort">
                <label for="select-checklist-sort">Sort by: </label>
                <select id="select-checklist-sort" class="form-control" v-model="sortField"
                        @change="changeSort(sortField)">
                    <option value="" selected disabled>Pick one</option>
                    <option value="name">Name</option>
                    <option value="recipient">Recipient</option>
                    <option value="created_at">Made</option>
                </select>
            </div>
        </div>
        <div id="page-scroll-content">
            <ul id="list-checklists" v-show="checklists.length > 0" class="list-unstyled">
                <li class="single-checklist" v-for="checklist in checklists">
                    <a :href="'/checklist/' + checklist.hash" class="checklist-link">
                        <div class="header">
                            <h4>{{ checklist.name }}</h4>
                            <span class="date-made">{{ checklist.created_at | easyDate }}</span>
                        </div>
                        <p class="recipient">{{ checklist.recipient }}</p>
                        <div class="stats">
                        <span class="file_count small">
                            <i class="fa fa-file-o"></i> {{ checklist.received }} / {{ checklist.requested_files.length }}
                        </span>
                        </div>
                    </a>
                </li>
            </ul>

            <p v-else class="text-muted">Sorry we couldn't find any lists for you. <a href="/checklist/make">Make</a> a
                new
                one or
                <a href="#" @click.prevent="clearSearch">clear</a> your search to see more.</p>

            <div class="page-controls">
                <per-page-picker :response="response" :req-function="fetchResults"></per-page-picker>
                <paginator :response="response" :req-function="fetchResults"></paginator>
            </div>
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
                checklists: [],
                sortField: ''
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