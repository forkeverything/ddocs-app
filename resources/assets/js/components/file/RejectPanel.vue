<template>
    <div class="file-reject">
        <form class="reject-panel panel panel-default panel-floating"
              v-if="isOwner"
              @submit.prevent="rejectFile(selectedFile)"
              v-show="visible"
              @click.stop=""
        >
            <div class="panel-heading">
                Reason / Changes Required
            </div>
            <div class="panel-body">
                <div class="form-group">
                                                <textarea rows="3" class="form-control autosize"
                                                          v-model="reason"></textarea>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-outline-grey btn-space btn-sm"
                            @click="toggleRejectPanel"
                    >Cancel
                    </button>
                    <button type="submit" class="btn btn-solid-red btn-sm">{{ rejectText }}</button>
                </div>
            </div>
        </form>
    </div>
</template>
<script>
    const replacesFileFromFilesArray = require('../../mixins/replacesFileFromFilesArray');

    export default {
        data: function () {
            return {
                ajaxReady: true,
                reason: ''
            }
        },
        computed: {
            rejectText: function() {
                if(! this.ajaxReady) return 'Saving...';
                return 'Reject';
            }
        },
        props: ['is-owner', 'files', 'selected-file', 'visible'],
        mixins: [replacesFileFromFilesArray],
        methods: {
            toggleRejectPanel: function () {
                this.showRejectPanel = !this.showRejectPanel;
            },
            rejectFile: function (file) {
                var self = this;
                if (!self.ajaxReady) return;
                self.ajaxReady = false;

                self.$http.post('/file/' + file.id + '/reject', {
                    reason: self.reason
                }).then((response) => {
                    // success
                    self.reason = '';
                    self.replaceFile(JSON.parse(response.data));
                    self.$set('selectedFile', JSON.parse(response.data));
                    self.numReceived--;
                    self.ajaxReady = true;
                    self.visible = false;
                }, (response) => {
                    // error
                    console.log(response);
                    self.ajaxReady = true;
                });
            }
        }
    }
</script>