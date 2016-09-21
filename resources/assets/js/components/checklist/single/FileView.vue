<template>
    <div id="file-view" class="content">

        <div id="selected-file-requirements">
            <selected-file-date :file-request.sync="fileRequests[selectedFileRequestIndex]"></selected-file-date>
            <selected-file-weighting :file-request.sync="fileRequests[selectedFileRequestIndex]"></selected-file-weighting>
        </div>
        <h4>{{ selectedFileRequest.name }}</h4>
        <div id="progress-status"
             :class="{
                                 received: ! selectedFileRequest.uploading && selectedFileRequest.status === 'received',
                                 rejected: ! selectedFileRequest.uploading && selectedFileRequest.status === 'rejected',
                                 }"
        >
            <div class="progress-bar"
                 :style="{
                                        width: selectedFileRequest.uploadProgress + '%'
                                     }"
            ></div>
        </div>
        <ul id="single-file-request-menu" class="list-inline list-unstyled">
            <li class="menu-item">
                <a href="#"
                   @click.prevent="showRejectModal"
                   :class="{ 'disabled': ! canRejectFile }">
                    <i class="icon reject fa fa-close"></i>Reject
                </a>
            </li>
            <li class="menu-item">
                <a :href="'/fr/' + selectedFileRequest.hash + '/history'"
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

        <file-request-notes :file-request.sync="fileRequests[selectedFileRequestIndex]"></file-request-notes>

    </div>
</template>
<script>
export default {
    data: function(){
        return {

        }
    },
    props: ['file-requests', 'selected-file-request-index', 'selected-file-request', 'show-reject-modal', 'can-reject-file', 'show-delete-modal'],
    methods: {

    }
}
</script>