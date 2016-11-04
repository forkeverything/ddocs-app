<template>
    <div id="file-view" class="content">
        <button type="button" class="close" aria-label="Close" @click="$emit('close')">
            <span aria-hidden="true">&times;</span>
        </button>
        <h3>{{ selectedFileRequest.name }}</h3>
        <div class="due-date">
            <i class="fa fa-calendar"></i><fr-due-date :checklist-belongs-to-user="checklistBelongsToUser"
                                                      :file-request="selectedFileRequest"
                                                      :index="selectedFileRequestIndex"
                                                      @update-file-request="updateFileRequest"
        ></fr-due-date>
        </div>
        <ul id="single-file-request-menu" class="list-inline list-unstyled">
            <li class="menu-item">
                <a href="#"
                   @click.prevent="$emit('reject')"
                   :class="{ 'disabled': ! canRejectFile }">
                    <i class="icon reject fa fa-close"></i>Reject
                </a>
            </li>
            <li class="menu-item">
                <a href="#"
                   :class="{'disabled': ! selectedFileRequest.latest_upload }"
                   @click.prevent="$emit('history')"
                >
                    <i class="icon history fa fa-clock-o"></i>History
                </a>
            </li>
            <li class="menu-item">
                <a href="#"
                   @click.prevent="$emit('delete')"
                >
                    <i class="icon delete fa fa-trash-o"></i>Delete
                </a>
            </li>
        </ul>

        <file-request-notes :file-request="selectedFileRequest"></file-request-notes>

        <div class="comments">
            <h5>Comments</h5>
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
        props: ['is-owner', 'selected-file-request-index', 'selected-file-request', 'can-reject-file', 'show-delete-modal', 'checklist-belongs-to-user'],
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
        },
        methods: {
            updateFileRequest(fileRequest, index) {
                this.$emit('update-file-request', fileRequest, index);
            }
        }
    }
</script>