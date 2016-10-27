<template>
    <div class="request-view">
        <div class="summary">
            <span class="visibility info">
                <i class="fa fa-eye"
                   data-toggle="tooltip" data-placement="bottom" title="Checklist recipients and team members"
                ></i>
            </span>
        </div>

        <div class="fr-file-name">
            <div class="title-group">
                <h5>Requested File</h5>
                <a class="clickable"
                   @click.prevent="$emit('detach-request')"
                ><i class="fa fa-unlink"></i>Detach
                </a>
            </div>
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
            <comments-thread :comments="comments" @add-comment="addComment" :loading="loadingComments" :saving="saving"></comments-thread>
        </div>
    </div>
</template>
<script>
    const hasComments = require('../../../../mixins/HasComments');
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
            this.$nextTick(() => {
                $('[data-toggle="tooltip"]').tooltip()
            });
        },
        mixins: [hasComments]
    }
</script>