<template>
    <div class="file-uploader">
        <button type="button"
                v-show="! fileRequestClone.uploading"
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
               @change="uploadFile(file, $event)"
        >
        <div class="loader" v-show="fileRequestClone.uploading">
            <i class="fa fa-spinner fa-pulse fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                fileRequestClone: ''
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
            uploadFile: function (file, $event) {

                let fd = new FormData();
                let uploadedFile = $event.srcElement.files[0];

                fd.append('file', uploadedFile);

                this.$http.post(`/api/file_requests/${this.fileRequest.hash}/upload`, fd, {
                    before() {
                        this.fileRequestClone.uploading = true;
                        this.fileRequestClone.uploadProgress = 0;
                        this.$emit('update-file-request', this.fileRequestClone, this.index);
                    },
                    progress(event) {

                        this.fileRequestClone.uploadProgress = Math.round(100 * event.loaded / event.total);
                        this.$emit('update-file-request', this.fileRequestClone, this.index);
                    }
                }).then((response) => {
                    let updatedFileRequest = response.json();
                    this.fileRequestClone.uploading = false;
                    this.$emit('update-file-request', updatedFileRequest, this.index);
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
            this.fileRequestClone = this.fileRequest;
            Vue.set(this.fileRequestClone, 'uploading', false);
            Vue.set(this.fileRequestClone, 'uploadProgress', 0);
        },
        beforeDestroy(){
            vueGlobalEventBus.$off('upload-selected-file-' + this.fileRequest.id, this.selectFile);
        }
    }
</script>