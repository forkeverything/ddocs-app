<template>
    <div class="history-modal">
        <div class="modal fade" tabindex="-1" role="dialog" ref="modal">
            <div class="modal-dialog" role="document">
                <div class="modal-content" v-if="selectedFileRequest">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title text-capitalize">{{ selectedFileRequest.name }} - Uploads History</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-standard table-hover">
                            <thead>
                            <tr>
                                <th>Version</th>
                                <th>Reason / Changes</th>
                                <th></th>
                            <tr>
                            </thead>
                            <tbody>

                            <template v-for="(upload, index) in uploads">
                                <tr>
                                    <td class="fit-to-content">{{ index + 1}}</td>
                                    <td>
                                        <span v-if="upload.rejected_reason">
                                            {{ upload.rejected_reason }}
                                        </span>
                                        <span v-if="! upload.rejected_reason">
                                            --
                                        </span>
                                    </td>
                                    <td class="fit-to-content">
                                        <a :href="awsURL + upload.path">
                                            <button type="button" class="btn btn-download btn-info btn-sm">
                                                <i class="fa fa-download"></i>
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
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
                awsURL: awsURL,
                ajaxReady: true,
                uploads: ''
            }
        },
        props: ['selected-file-request'],
        computed: {},
        methods: {
            fetchUploads(){
                this.$http.get(`/api/file_requests/${ this.selectedFileRequest.hash }/uploads`, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.uploads = response.json();
                }, (response) => {
                    // error
                    console.log('Error fetching from: /file_requests/uploads');
                });
            }
        },
        created() {
            vueGlobalEventBus.$on('show-history-modal', () => $(this.$refs.modal).modal('show'));
        },
        mounted() {
            $(this.$refs.modal).on('shown.bs.modal', this.fetchUploads);
        },
        beforeDestroy(){
            vueGlobalEventBus.$off('show-history-modal');
        }
    }
</script>
