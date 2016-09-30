<template>
<div id="project-file-modal">
    <div class="modal" tabindex="-1" role="dialog" v-el:modal>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <div class="main">
                        <div class="content">
                            <h3 class="file-name"><i class="fa fa-file-o"></i> <span class="name-wrap"><editable-text-field :value.sync="file.name"></editable-text-field></span></h3>
                            <ul class="list-unstyled list-inline file-modal-nav">
                                <li><a class="clickable active"><h4>Project</h4></a></li>
                                <li><a class="clickable" :class="{ disabled: ! attached }" :disabled="! attached" data-toggle="tooltip" data-placement="bottom" title="File request info"><h4>Request</h4></a></li>
                                <li class="dropdown mobile-actions">
                                    <a class="btn-dropdown clickable" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">•••</a>
                                    <ul class="dropdown-menu list-unstyled dropdown-menu-right">
                                        <li class="heading"><h5>Request</h5></li>
                                        <li><a class="clickable" :disabled="attached" data-toggle="tooltip" data-placement="left" title="link file request"><i class="fa fa-link"></i>Attach</a></li>
                                        <li><a class="clickable" :disabled="! attached"><i class="fa fa-list"></i>Checklist</a></li>
                                        <li class="heading"><h5>Project</h5></li>
                                        <li><a class="clickable"><i class="fa fa-upload" data-toggle="tooltip" data-placement="left" title="Add internal file"></i>Upload</a></li>
                                        <li><a class="clickable"><i class="fa fa-trash"></i>Delete</a></li>
                                    </ul>
                                </li>
                            </ul>
                            <div class="project">
                                <span class="small text-muted">Only team members in the project can see this section.</span>
                                <div class="description">
                                    <h5>Description</h5>
                                    <editable-text-area :value.sync="file.description" :allow-null="true" :placeholder="'Details about the file...'"></editable-text-area>
                                </div>
                                <div class="comments">
                                    <h5>Team Comments</h5>
                                    <comments-thread :project-id="projectId" :comments.sync="file.comments" :subject-type="'App\\ProjectFile'" :subject-id="file.id"></comments-thread>
                                </div>
                            </div>
                        </div>
                        <div class="actions">
                            <ul class="list-unstyled">
                                <li><h5>Request</h5></li>
                                <li><a class="btn btn-primary btn-sm" :disabled="attached" data-toggle="tooltip" data-placement="right" title="link file request"><i class="fa fa-link"></i>Attach</a></li>
                                <li><a class="btn btn-primary btn-sm" :disabled="! attached"><i class="fa fa-list"></i>Checklist</a></li>
                                <li><h5>Project</h5></li>
                                <li><a class="btn btn-primary btn-sm"><i class="fa fa-upload" data-toggle="tooltip" data-placement="right" title="Add internal file"></i>Upload</a></li>
                                <li><a class="btn btn-primary btn-sm"><i class="fa fa-trash"></i>Delete</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>
<script>
export default {
    data: function(){
        return {
            file: ''
        }
    },
    computed: {
       attached(){
           return this.file_request_id
       }
    },
    props: ['project-id'],
    methods: {
        hide(){
            $(this.$els.modal).modal('hide');
        }
    },
    ready() {
        vueGlobalEventBus.$on('view-project-file', (file) => {
            this.file = file;
            this.$nextTick(() => {
                $(this.$els.modal).modal('show');
            vueGlobalEventBus.$emit('showing-comments');
            });
        })
    }
}
</script>