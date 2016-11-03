<template>
    <div class="actions">
        <ul class="list-pf-modal-actions list-unstyled">
            <li class="heading"><h5>Request</h5></li>

            <li class="attach-wrapper">
                <button type="button"
                        class="btn btn-default btn-modal-action btn-sm"
                        @click.stop="toggleAttachFRMenu"
                        :disabled="attached"
                >
                    <i class="fa fa-link"></i>Attach
                </button>
                <attach-fr-dropdown v-if="attachFileRequestMenu"
                                    :file="file"
                                    @attached-request="attachedRequest"
                                    @toggle-attach-fr-menu="toggleAttachFRMenu"
                >
                </attach-fr-dropdown>
            </li>
            <li>
                <a class="btn btn-default btn-modal-action btn-sm"
                   :disabled="! attached"
                   @click="$emit('go-to-checklist')"
                >
                    <i class="fa fa-list"></i>Checklist
                </a>
            </li>
            <li class="heading"><h5>Project</h5></li>
            <li>
                <pf-uploader :project-file="file"
                             :can-upload="canUpload"
                             @uploaded-file="updateUploads"
                ></pf-uploader>
            </li>
            <li>
                <pf-downloader :uploads="file.uploads"></pf-downloader>
            </li>
            <li>
                <button type="button"
                        class="btn btn-default btn-modal-action btn-sm"
                        @click="deleteFile"
                >
                    <i class="fa fa-trash"></i>Delete
                </button>
            </li>
        </ul>
    </div>
</template>
<script>
export default {
    data: function(){
        return {
            attachFileRequestMenu: false
        }
    },
    computed: {
        project() {
            return this.$store.state.project.data;
        },
        canUpload() {
            return ! this.attached;
        }
    },
    props: ['file', 'attached'],
    methods: {
        toggleAttachFRMenu() {
            this.attachFileRequestMenu = !this.attachFileRequestMenu
        },
        attachedRequest(projectFile) {
            // update our project file to include the file request
            this.$emit('set-file', projectFile);
            this.attachFileRequestMenu = false;
            this.$emit('update-file-request', projectFile.file_request);
            this.$nextTick(() => {
                this.$emit('switch-view', 'pfm-request-view');
            });
        },
        updateUploads(projectFile) {
            // If we're viewing a new
            if (projectFile.id !== this.file.id) return;
            // Uploaded a new file directly, update the
            // uploads relation
            this.file.uploads = projectFile.uploads;
        },
        deleteFile(){
            this.$http.delete(`/api/projects/${ this.project.id }/files/${ this.file.id }`).then((res) => {
                vueGlobalEventBus.$emit('delete-project-file', this.file.id);
                this.$nextTick(this.$emit('hide'));
            }, (res) => {
                console.log(' error deleting file.')
            });
        }
    }
}
</script>