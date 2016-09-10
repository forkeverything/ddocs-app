<template>
    <div class="reject-modal keep-selected-file">
        <div class="modal fade" tabindex="-1" role="dialog" v-el:modal>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4>Reason / Changes Required</h4>
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
        props: ['file'],
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
            rejectFile: function () {
                var self = this;
                if (!self.ajaxReady) return;
                self.ajaxReady = false;

                self.$http.post('/fr/' + this.file.hash + '/reject', {
                    reason: self.reason
                }).then((response) => {
                    // success
                    self.reason = '';

                    // We'll update the file model through an event because the prop we're receiving is
                    // a computed property. If we used a setter() on the property, it would re-init
                    // everytime we selected a file, even without any changes.
                    vueGlobalEventBus.$emit('updated-file-request', JSON.parse(response.data));

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