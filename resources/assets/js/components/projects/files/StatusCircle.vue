<template>
    <div class="pf-status-circle">
        <div class="circle"
             :class="status"
        ></div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {}
        },
        computed: {
            fileRequest() {
                return this.projectFile.file_request;
            },
            late() {
                if (!this.projectFile.due) return false;
                let dueDate = moment(this.projectFile.due, "YYYY-MM-DD HH:mm:ss");
                return dueDate.isBefore(moment(), 'd');
            },
            veryLate() {
                if (!this.projectFile.due) return false;
                let dueDate = moment(this.projectFile.due, "YYYY-MM-DD HH:mm:ss");
                return dueDate.isBefore(moment().subtract(3, 'days'), 'd');
            },
            hasDirectUploads() {
                return this.projectFile.meta.num_uploads;
            },
            status() {
                if (this.fileRequest) {
                    if(this.fileRequest.status === 'rejected') return 'danger';
                    if(this.veryLate && this.fileRequest.status !== 'received') return 'danger';
                    if(this.late && this.fileRequest.status !== 'received') return 'warning';
                    if(this.fileRequest.status === 'received') return 'success';
                } else {
                    // is it late and no uploads?
                    if(this.veryLate && ! this.hasDirectUploads) return 'danger';
                    // is it due soon?
                    if(this.late && ! this.hasDirectUploads) return 'warning';
                    // do we have uploads
                    if(this.hasDirectUploads) return 'success';
                }


            }
        },
        props: ['project-file'],
        methods: {}
    }
</script>