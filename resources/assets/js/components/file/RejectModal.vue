<template>
    <div class="reject-modal keep-selected-file">
        <div class="modal fade" tabindex="-1" role="dialog" v-el:modal>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Reason / Changes Required</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                                                <textarea rows="3" class="form-control autosize" v-el:text-area
                                                          v-model="reason"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-space btn-sm"
                                @click="hideModal"
                                data-dismiss="modal"
                        >Cancel
                        </button>
                        <button type="submit" class="btn btn-danger btn-sm" @click="rejectFile">{{ submitText }}
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
                reason: ''
            }
        },
        props: ['file-requests', 'selected-file-request'],
        computed: {
            submitText: function () {
                if (!this.ajaxReady) return 'Saving...';
                return 'Reject';
            }
        },
        methods: {
            hideModal: function() {
                this.reason = '';
            },
            getFileRequestIndex(file) {
                return _.indexOf(this.fileRequests, _.find(this.fileRequests, {id: file.id}));
            },
            rejectFile: function () {
                var self = this;
                if (!self.ajaxReady) return;
                self.ajaxReady = false;

                self.$http.post('/fr/' + this.selectedFileRequest.hash + '/reject', {
                    reason: self.reason
                }).then((response) => {
                    // success
                    self.reason = '';

                    let updatedFileRequest = response.json();
                    let index = this.getFileRequestIndex(updatedFileRequest);
                    this.$set('fileRequests[' + index + ']', updatedFileRequest);

                    vueGlobalEventBus.$emit('updated-weighting');
                    
                    $(this.$els.modal).modal('hide');
                    self.ajaxReady = true;
                }, (response) => {
                    // error
                    console.log(response);
                    self.ajaxReady = true;
                    $(this.$els.modal).modal('hide');
                });
            }
        },
        ready: function () {
            $(this.$els.modal).on('shown.bs.modal', () => $(this.$els.textArea).focus());

            vueGlobalEventBus.$on('show-reject-modal', () => $(this.$els.modal).modal('show'));
        }
    }
</script>