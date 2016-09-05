<template>
    <div class="active-filters">

        <!-- Active Filter: Version -->
        <button type="button" v-if="params.version_filter_integer" class="btn button-remove-filter" @click="
                        removeFilter('version')">
            <span class="field">File Version: </span><span
                v-if="params.version_filter_integer[0]">{{ params.version_filter_integer[0] }}</span><span
                v-else>~ </span><span
                v-if="params.version_filter_integer[0] && params.version_filter_integer[1]"> - </span><span
                v-if="params.version_filter_integer[1]">{{ params.version_filter_integer[1] }}</span><span
                v-else> ~</span></button>

        <!-- Active Filter: Due (date) -->
        <button type="button" v-if="params.due_filter_date" class="btn button-remove-filter" @click="
                        removeFilter('due')"><span
                class="field">Due: </span><span v-if="params.due_filter_date[0]">{{ params.due_filter_date[0] | date }}</span>
            <span v-else>~ </span><span
                    v-if="params.due_filter_date[0] && params.due_filter_date[1]"> - </span><span
                    v-if="params.due_filter_date[1]">{{ params.due_filter_date[1] | date }}</span><span
                    v-else> ~</span></button>

        <!-- Active Filter: Status -->
        <button type="button" v-if="params.status" class="btn button-remove-filter"
                @click="removeFilter('status')">
            Status: {{ params.status }}
        </button>
    </div>
</template>
<script>
export default {
    props: ['params', 'remove-filter']
}
</script>