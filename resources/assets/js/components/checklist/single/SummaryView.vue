<template>
    <div id="summary-view" class="content">
        <h4 class="title-list-overview">List Overview</h4>
        <div id="description" v-if="checklist">
            <h5>Description</h5>
            <p v-if="checklist.description">{{ checklist.description }}</p>
        </div>
        <div id="files-count" v-if="checklist">
            <h5>Files Recevied</h5>
            <p>{{ checklist.meta.num_received }} / {{ checklist.meta.num_total }}</p>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {}
        },
        props: ['checklist'],
        methods: {},
        created() {
        },
        mounted() {

        },
        beforeDestroy(){

        }
    }
</script>