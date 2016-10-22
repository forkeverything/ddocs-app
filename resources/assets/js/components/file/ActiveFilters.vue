<template>
    <div class="active-filters">
        <ul v-if="params" class="list-active-filters list-unstyled list-inline" v-show="withFilters">
            <!-- Active Filter: Version -->
            <li v-if="params.version_filter_integer" class="single-filter">
                <div class="text">
                    <span class="field">File Version: </span>
                    <span v-if="params.version_filter_integer[0]">{{ params.version_filter_integer[0] }}</span>
                    <span v-if="! params.version_filter_integer[0]">~ </span><span
                        v-if="params.version_filter_integer[0] && params.version_filter_integer[1]"> - </span>
                    <span v-if="params.version_filter_integer[1]">{{ params.version_filter_integer[1] }}</span>
                    <span v-if="! params.version_filter_integer[1]"> ~</span>
                </div>
                <button type="button" class="btn close" @click="
                        removeFilter('version')">&times;</button>
            </li>

            <!-- Active Filter: Due (date) -->
            <li v-if="params.due_filter_date" class="single-filter">
                <div class="text">
                    <span class="field">Due: </span>
                    <span v-if="params.due_filter_date[0]">{{ params.due_filter_date[0] | date }}</span>
                    <span v-if="! params.due_filter_date[0]">~ </span>
                    <span v-if="params.due_filter_date[0] && params.due_filter_date[1]"> - </span>
                    <span v-if="params.due_filter_date[1]">{{ params.due_filter_date[1] | date }}</span>
                    <span v-if="! params.due_filter_date[1]"> ~</span>
                </div>
                <button type="button" class="btn close" @click="removeFilter('due')">&times;</button>
            </li>

            <!-- Active Filter: Status -->
            <li v-if="params.status" class="single-filter">
                <div class="text">Status: {{ params.status }}</div>
                <button type="button" class="btn close" @click="removeFilter('status')">&times;</button>
            </li>
        </ul>
    </div>
</template>
<script>
    export default {
        computed: {
            withFilters: function () {
                // look out for these properties that indicate we're filtering the data
                return this.params.version_filter_integer || this.params.status || this.params.due_filter_date;
            }
        },
        props: ['params', 'remove-filter']
    }
</script>