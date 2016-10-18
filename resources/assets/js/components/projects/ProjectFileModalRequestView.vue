<template>
    <div class="request-view">
        <p class="text-muted small"><i class="fa fa-eye"></i> Checklist recipients and team members</p>
        <div class="fr-file-name">
            <h5>Requested File</h5>
            <a class="clickable small" @click.prevent="$emit('go-to-checklist')">{{ fileRequest.checklist.name}}</a>
            <br>
            {{ fileRequest.name }}
        </div>
        <div class="recipients" v-if="fileRequest">
            <h5>Recipients</h5>
            <checklist-recipients :recipients="fileRequest.checklist.recipients"></checklist-recipients>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {}
        },
        computed: {
            fileRequest() {
                return this.file.file_request
            }
        },
        props: ['file'],
        methods: {

        }
    }
</script>