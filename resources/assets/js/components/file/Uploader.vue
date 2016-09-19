<template>
    <div class="file-uploader">
        <button type="button"
                v-show="! uploading"
                class="btn btn-primary"
                v-el:upload-button
                :disabled="alreadyReceivedFile"
                @click="selectFile"
        >
            <i class="fa fa-upload"></i>
        </button>
        <input v-el:input
               type="file"
               name="file"
               class="input-file-upload hide"
               @change="uploadFile(file, $event)"
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
        props: ['file-request'],
        methods: {
            selectFile: function () {
                $(this.$els.input).click();
            },
            uploadFile: function (file, $event) {

                var self = this;

                self.uploading = true;

                var fd = new FormData();
                var uploadedFile = $event.srcElement.files[0];

                fd.append('file', uploadedFile);

                self.$http.post('/fr/' + self.fileRequest.hash + '/upload', fd, {
                    before() {
                        self.$set('fileRequest.uploading', self.uploading);
                        self.$set('fileRequest.uploadProgress', 0);
                    },
                    progress(event) {
                        self.$set('fileRequest.uploadProgress', Math.round(100 * event.loaded / event.total));
                    }
                }).then((response) => {
                    self.fileRequest = JSON.parse(response.data);
                    self.uploading = false;
                    self.$set('fileRequest.uploading', self.uploading);
                    vueGlobalEventBus.$emit('updated-weighting');
                }, (response) => {
                    console.log('Upload file error.');
                    console.log(response);
                    self.uploading = false;
                });
            }
        },
        ready: function() {
            vueGlobalEventBus.$on('upload-selected-file-' + this.fileRequest.id, this.selectFile);
        }
    }
</script>