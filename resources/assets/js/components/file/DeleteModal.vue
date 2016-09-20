<template>
    <div class="delete-modal keep-selected-file">
        <div class="modal fade" tabindex="-1" role="dialog" v-el:modal>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Are you sure?</h4>
                    </div>
                    <div class="modal-body">
                        <p>Deleting a file request will also irreversibly remove all previously uploaded files.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-default btn-space btn-sm"
                                data-dismiss="modal"
                        >
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-danger btn-sm" @click="deleteFile">{{ submitText }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                ajaxReady: true,
            }
        },
        props: ['file-requests', 'selected-file-request'],
        computed: {
            submitText: function () {
                if (!this.ajaxReady) return 'Processing...';
                return 'Delete';
            }
        },
        methods: {
            getFileRequestIndex(file) {
                return _.indexOf(this.fileRequests, _.find(this.fileRequests, {id: file.id}));
            },
            deleteFile: function () {
                var self = this;
                if (!self.ajaxReady) return;

                self.$http.delete('/fr/' + this.selectedFileRequest.hash)
                        .then((response) => {
                            let updatedFileRequest = response.json();
                            let index = this.getFileRequestIndex(updatedFileRequest);
                            this.fileRequests.splice(index, 1);
                            this.selectedFileRequestIndex = '';
                            vueGlobalEventBus.$emit('updated-weighting');
                            $(this.$els.modal).modal('hide');
                            self.ajaxReady = true;
                        },(response) => {
                            // error
                            console.log(response);
                            $(this.$els.modal).modal('hide');
                            self.ajaxReady = true;
                        });
                }
            },
            ready: function () {
                vueGlobalEventBus.$on('show-delete-modal', () => $(this.$els.modal).modal('show'));
            }
        }
</script>