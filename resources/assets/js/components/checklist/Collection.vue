<template>

    <div id="checklist-all" class="container-fluid">
        <div class="header">
            <h3>
                Your Checklists
            </h3>
            <div>
                <router-link to="/checklists/make">
                    <button type="button" class="btn btn-info">New Checklist</button>
                </router-link>
            </div>
        </div>
        <br>
        <div id="checklist-collection">

            <form v-if="params" id="form-all-checklists-search" @submit.prevent="searchTerm">
                <div class="form-group">
                    <input class="form-control input-search"
                           type="text"
                           placeholder="Search..."
                           @keyup="searchTerm"
                           v-model="repoSearch"
                           :class="{
                                    'active': repoSearch && repoSearch.length > 0
                               }"
                    >
                </div>
            </form>

            <div id="checklist-sort">
                <label for="select-checklist-sort">Sort by: </label>
                <select id="select-checklist-sort" class="form-control" v-model="sortField"
                        @change="changeSort(sortField)">
                    <option value="" disabled>Pick one</option>
                    <option value="name">Name</option>
                    <option value="recipient">Recipient</option>
                    <option value="created_at">Made</option>
                </select>
            </div>


            <ul id="list-checklists" v-show="checklists" class="list-unstyled" @scroll="scrollList">
                <li class="single-checklist" v-for="checklist in checklists">

                    <router-link :to="'/c/' + checklist.hash" class="checklist-link">
                        <div class="header">
                            <h4>{{ checklist.name }}</h4>
                            <span class="date-made">{{ checklist.created_at | easyDate }}</span>
                        </div>
                        <p class="recipient">{{ checklist.recipient }}</p>
                        <div class="stats">
                        <span class="file_count small">
                            <i class="fa fa-file-o"></i> {{ checklist.meta.num_received }} / {{ checklist.meta.num_total }}
                        </span>
                        </div>
                    </router-link>
                </li>
                <li v-if="checklists && checklists.length < 1" class="text-muted text-center">
                    <br>
                    Sorry we couldn't find any lists for you. <router-link to="/checklists/make">Make</router-link> a new one or
                    <a href="#" @click.prevent="clearSearch">clear</a> your search to see more.
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
    const fetchesFromEloquentRepository = require('../../mixins/fetchesFromEloquentRepository');

    export default {
        name: 'ChecklistsCollection',
        data: function () {
            return {
                ajaxReady: true,
                hasFilters: false,
                requestUrl: "api/checklists",
                sortField: '',
                container: 'list-checklists'
            }
        },
        computed: {
            checklists() {
                return this.response.data;
            }
        },
        methods: {

        },
        mixins: [fetchesFromEloquentRepository],
        mounted() {

        }
    };
</script>