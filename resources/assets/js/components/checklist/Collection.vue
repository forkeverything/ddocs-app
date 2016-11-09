<template>
    <div id="checklist-all" class="main-content">
        <div class="text-right header">
            <router-link to="/checklists/make">
                <button type="button" class="btn btn-info"><i class="fa fa-plus"></i> Checklist</button>
            </router-link>
        </div>
        <div id="checklist-collection">

            <rectangle-loader :loading="initializingRepo"></rectangle-loader>

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
                    <option value="created_at">Date Created</option>
                </select>
            </div>
            <ul id="list-checklists" class="list-unstyled" @scroll="scrollList">
                <li v-for="checklist in checklists"
                    class="single-checklist clickable"
                    @click="goToChecklist(checklist)"
                >
                    <div class="main">
                        <p class="name">{{ checklist.name }}</p>
                        <span class="date-made">{{ checklist.created_at | easyDate }}</span>
                    </div>
                    <ul class="stats list-unstyled list-inline">
                        <li class="file_count">
                            <i class="fa fa-file"></i>{{ checklist.meta.num_received }} / {{ checklist.meta.num_total }}
                        </li>
                        <stats-recipients-dropdown :checklist="checklist"></stats-recipients-dropdown>
                    </ul>
                </li>
                <li v-if="checklists && checklists.length < 1" class="text-muted text-center">
                    <br>
                    Sorry we couldn't find any lists for you.
                    <router-link to="/checklists/make">Make</router-link>
                    a new one or
                    <a href="#" @click.prevent="clearSearch">clear</a> your search to see more.
                </li>
                <li id="checklist-loading" v-if="hasNextPage">
                    <rectangle-loader :loading="true" size="small"></rectangle-loader>
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
            goToChecklist(checklist) {
                router.push('/c/' + checklist.hash);
            }
        },
        mixins: [fetchesFromEloquentRepository],
        mounted() {
            this.$store.commit('setTitle', 'Checklists');
        }
    };
</script>