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
                <a href="#"
                   class="secondary"
                   @click.prevent="$emit('detach-request')"
                ><i class="fa fa-unlink"></i>Detach
                </a>
            </div>
            <a class="checklist secondary" href="#" @click.prevent="$emit('go-to-checklist')">{{ fileRequest.checklist.name}}</a>
            <br>
            <h4>
                <i class="fa fa-file-o" :class="fileRequest.status"></i>
                <a v-if="fileRequest.latest_upload"
                   :href=" awsURL + fileRequest.latest_upload.path"
                   :alt="fileRequest.name + 'download link'"
                >
                    {{ fileRequest.name }}
                </a>
                <span v-if="! fileRequest.latest_upload">{{ fileRequest.name }}</span>
            </h4>
        </div>

        <div class="recipients" v-if="fileRequest">
            <h5>Recipients</h5>
            <ul class="list-recipients list-unstyled">
                <li v-for="recipient in fileRequest.checklist.recipients">
                    {{ recipient.email }}
                </li>
            </ul>
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
            return {
                awsURL: awsURL
            }
        },
        computed: {
            fileRequest() {
                return this.file.file_request
            },
            commentsUrl() {
                return `/api/comments/file_request/${this.fileRequest.hash}`;
            },
            canRejectFile() {
                return this.fileRequest.status === 'received';
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