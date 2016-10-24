<template>
    <div class="fr-uploader">
        <button type="button"
                v-show="! uploading"
                class="btn btn-primary"
                :disabled="alreadyReceivedFile"
                @click="selectFile"
        >
            <i class="fa fa-upload"></i>
        </button>
        <input ref="input"
               type="file"
               name="file"
               class="input-file-upload hide"
               @change="uploadFile($event)"
        >
        <div class="loader" v-show="uploading">
            <i class="fa fa-spinner fa-pulse fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                uploading: false
            }
        },
        computed: {
            alreadyReceivedFile: function () {
                return this.fileRequest.status === 'received';
            }
        },
        props: ['file-request', 'index'],
        methods: {
            selectFile: function () {
                $(this.$refs.input).click();
            },
            uploadFile: function ($event) {

                let fd = new FormData();
                let uploadedFile = $event.srcElement.files[0];
                fd.append('file', uploadedFile);

                this.uploading = true;

                this.$http.post(`/api/file_requests/${this.fileRequest.hash}/upload`, fd, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    },
                    progress(event) {
                        //  let progress = Math.round(100 * event.loaded / event.total);
                    }
                }).then((response) => {
                    let updatedFileRequest = response.json();
                    this.$emit('update-file-request', updatedFileRequest, this.index);
                    this.uploading = false;
                }, (response) => {
                    console.log('Upload file error.');
                    console.log(response);
                    this.uploading = false;
                });
            }
        },
        created() {
            vueGlobalEventBus.$on('upload-selected-file-' + this.fileRequest.id, this.selectFile);
        },
        mounted() {

        },
        beforeDestroy(){
            vueGlobalEventBus.$off('upload-selected-file-' + this.fileRequest.id, this.selectFile);
        }
    }
</script>