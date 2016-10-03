<template>

    <div id="checklist-collection">

        <form v-if="params" id="form-all-checklists-search" @submit.prevent="searchTerm">
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


        <ul id="list-checklists" v-show="checklists" class="list-unstyled" @scroll="scrollList">
            <li class="single-checklist" v-for="checklist in checklists">
                <a :href="'/c/' + checklist.hash" class="checklist-link">
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
            <li v-if="checklists && checklists.length < 1" class="text-muted text-center">
                <br>
                Sorry we couldn't find any lists for you. <a href="/c/make">Make</a> a new one or
                <a href="#" @click.prevent="clearSearch">clear</a> your search to see more.
            </li>
        </ul>
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
                requestUrl: "c/get",
                sortField: '',
                container: 'list-checklists',
            }
        },
        computed: {
            checklists() {
                return this.response.data;
            }
        },
        methods: {
            scrollList: _.throttle(function (event) {
                let el = document.getElementById('list-checklists');
                if ($(el).innerHeight() + $(el).scrollTop() >= (el.scrollHeight - 100)) this.fetchNextPage();
            }, 100)
        },
        mixins: [fetchesFromEloquentRepository],
        mounted() {
            this.$nextTick(this.scrollList);
        }
    };
</script>