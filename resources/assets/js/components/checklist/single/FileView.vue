<template>
    <div id="file-view" class="content">
        <h4>{{ selectedFileRequest.name }}</h4>
        <ul id="single-file-request-menu" class="list-inline list-unstyled">
            <li class="menu-item">
                <a href="#"
                   @click.prevent="showRejectModal"
                   :class="{ 'disabled': ! canRejectFile }">
                    <i class="icon reject fa fa-close"></i>Reject
                </a>
            </li>
            <li class="menu-item">
                <a :href="'/file_requests/' + selectedFileRequest.hash + '/history'"
                   :class="{'disabled': ! selectedFileRequest.latest_upload }">
                    <i class="icon history fa fa-clock-o"></i>History
                </a>
            </li>
            <li class="menu-item">
                <a href="#"
                   @click.prevent="showDeleteModal"
                >
                    <i class="icon delete fa fa-trash-o"></i>Delete
                </a>
            </li>
        </ul>

        <file-request-notes :file-request="selectedFileRequest"></file-request-notes>

        <div class="comments">
            <comments-thread :comments="comments"
                             @add-comment="addComment"
                             :loading="loadingComments"
                             :saving="saving"
            >
            </comments-thread>
        </div>
    </div>
</template>
<script>
    const hasComments = require('../../../mixins/HasComments');
    export default {
        data: function () {
            return {}
        },
        props: ['is-owner', 'selected-file-request-index', 'selected-file-request', 'show-reject-modal', 'can-reject-file', 'show-delete-modal'],
        computed: {
            commentsUrl() {
                return `/api/comments/file_request/${ this.selectedFileRequest.hash }`;
            }
        },
        watch: {
            selectedFileRequest(fileRequest) {
                if(fileRequest) this.fetchComments();
            }
        },
        mixins: [hasComments],
        mounted() {
            this.fetchComments();
        }
    }
</script>