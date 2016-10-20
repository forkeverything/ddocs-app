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
        <div class="notes">
            <h5>Notes</h5>
            <file-request-notes :file-request="fileRequest"></file-request-notes>
        </div>
        <div class="comments">
            <h5>Request Comments</h5>
            <comments-thread :comments="comments" @add-comment="addComment" :loading="loadingComments"></comments-thread>
        </div>
    </div>
</template>
<script>
    const hasComments = require('../../mixins/HasComments');
    export default {
        data: function () {
            return {}
        },
        computed: {
            fileRequest() {
                return this.file.file_request
            },
            commentsUrl() {
                return `/api/comments/file_request/${this.fileRequest.hash}`;
            }
        },
        props: ['file'],
        methods: {

        },
        mounted() {
            this.fetchComments();
        },
        mixins: [hasComments]
    }
</script>