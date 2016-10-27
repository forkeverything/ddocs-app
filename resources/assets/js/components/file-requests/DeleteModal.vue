<template>
    <div class="delete-modal">
        <div class="modal fade" tabindex="-1" role="dialog" ref="modal">
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
        props: ['selected-file-request', 'index'],
        computed: {
            submitText: function () {
                if (!this.ajaxReady) return 'Processing...';
                return 'Delete';
            }
        },
        methods: {
            deleteFile: function () {

                if (!this.ajaxReady) return;

                this.$http.delete('/api/file_requests/' + this.selectedFileRequest.hash, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                })
                        .then((response) => {
                            this.$emit('remove-file-request', this.index);
                            $(this.$refs.modal).modal('hide');
                            this.ajaxReady = true;
                        }, (response) => {
                            // error
                            console.log(response);
                            $(this.$refs.modal).modal('hide');
                            this.ajaxReady = true;
                        });
            }
        },
        created() {
            vueGlobalEventBus.$on('show-delete-modal', () => $(this.$refs.modal).modal('show'));
        },
        beforeDestroy(){
            vueGlobalEventBus.$off('show-delete-modal');
        }
    }
</script>