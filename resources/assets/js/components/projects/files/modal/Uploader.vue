<template>
<div class="pf-uploader">
    <button type="button"
            class="btn btn-default btn-modal-action btn-sm"
            :disabled="! canUpload"
            @click="selectFile"
    >
        <i class="fa fa-upload"></i>Upload
    </button>
    <input type="file"
           class="input-pf-upload hidden"
           ref="input"
           name="file"
           @change="upload($event)"
    >
    <rectangle-loader :loading="uploading" size="small"></rectangle-loader>
</div>
</template>
<script>
export default {
    data: function(){
        return {
            uploading: false
        }
    },
    props: ['project-id', 'project-file', 'can-upload'],
    watch: {
        projectFile() {
            // whenever we get a new file, set uploading to false
            this.uploading = false;
        }
    },
    methods: {
        selectFile: function () {
            $(this.$refs.input).click();
        },
        upload($event) {

            let fd = new FormData();
            let uploadedFile = $event.srcElement.files[0];
            fd.append('file', uploadedFile);

            this.uploading = true;

            this.$http.post(`/api/projects/${this.projectId}/files/${ this.projectFile.id }/upload`, fd, {
                before(xhr) {
                    RequestsMonitor.pushOntoQueue(xhr);
                },
                progress(event) {
                    //  let progress = Math.round(100 * event.loaded / event.total);
                }
            }).then((response) => {
                let projectFile = response.json();
                // Update ProjectFile in modal
                this.$emit('uploaded-file', {
                    id: projectFile.id,
                    uploads: projectFile.uploads,
                    meta: projectFile.meta
                });
                // Update ProjectFile on the board
                vueGlobalEventBus.$emit('update-project-file', {
                    id: projectFile.id,
                    meta: projectFile.meta
                });
                this.uploading = false;
            }, (response) => {
                console.log('Error uploading project file.');
                console.log(response);
                this.uploading = false;
            });
        }
    }
}
</script>