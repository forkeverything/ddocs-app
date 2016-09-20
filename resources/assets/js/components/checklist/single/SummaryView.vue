<template>
    <div id="summary-view" class="content">
        <h4 class="title-list-overview">List Overview</h4>
        <div id="description">
            <h5>Description</h5>
            <p v-if="checklist.description">{{ checklist.description }}</p>
        </div>
        <div id="files-count">
            <h5>Files Recevied</h5>
            <p>{{ checklist.received }} / {{ checklist.requested_files.length }}</p>
        </div>
        <div id="list-weighting" v-if="checklist.weightings.set">
            <h5>Progress</h5>
            <p>
                Completed {{ checklist.weightings.progress }}% out of a total of {{
                checklist.weightings.total }}%
            </p>
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
        ready() {
            // Update checklist weightings
            vueGlobalEventBus.$on('updated-weighting', () => {
                this.$http.get('/c/' + this.checklist.hash + '/weightings')
                        .then((response) => {
                            // Success
                            this.checklist.weightings = JSON.parse(response.data);
                        }, (response) => {
                            // error
                            console.log('GET REQ Error!');
                            console.log(response);
                        })
            });
        }
    }
</script>