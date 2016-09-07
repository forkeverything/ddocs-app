<template>
    <div class="file-uploader">
        <button type="button"
                v-show="! progress"
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
        <span class="upload-percentage"
              v-show="progress"
        >{{ progress }} %</span>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                progress: ''
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

                var fd = new FormData();
                var uploadedFile = $event.srcElement.files[0];

                fd.append('file', uploadedFile);

                self.$http.post('/file/' + self.fileRequest.id, fd, {
                    progress: (event) => {
                        self.progress = Math.round(100 * event.loaded / event.total);
                    }
                }).then((response) => {
                    self.progress = '';
                    self.fileRequest = JSON.parse(response.data);

                }, (response) => {
                    self.progress = '';
                    console.log('Upload file error.');
                    console.log(response);
                });
            }
        }
    }
</script>