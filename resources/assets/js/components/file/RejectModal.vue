<template>
    <div class="reject-modal keep-selected-file">
        <div class="modal fade" tabindex="-1" role="dialog" ref="modal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Reason / Changes Required</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <textarea rows="3"
                                      class="form-control autosize"
                                      ref="text-area"
                                      v-model="reason"
                            >
                            </textarea>
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
        props: ['index', 'selected-file-request'],
        computed: {
            submitText: function () {
                if (!this.ajaxReady) return 'Saving...';
                return 'Reject';
            }
        },
        methods: {
            hideModal: function () {
                this.reason = '';
            },
            rejectFile: function () {

                if (!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.post('/fr/' + this.selectedFileRequest.hash + '/reject', {
                    reason: this.reason
                }).then((response) => {
                    // success
                    this.reason = '';
                    this.$emit('update-file-request', response.json(), this.index);
                    $(this.$refs.modal).modal('hide');
                    this.ajaxReady = true;
                }, (response) => {
                    // error
                    console.log(response);
                    this.ajaxReady = true;
                    $(this.$refs.modal).modal('hide');
                });
            }
        },
        created() {
            vueGlobalEventBus.$on('show-reject-modal', () => $(this.$refs.modal).modal('show'));
        },
        mounted() {
            $(this.$refs.modal).on('shown.bs.modal', () => $(this.$refs.textArea).focus());
        },
        beforeDestroy(){
            vueGlobalEventBus.$off('show-reject-modal');
        }
    }
</script>