<template>
    <div class="project-folder-body">
        <div class="folder-header">
            <div class="folder-name">
                <editable-text-field :value.sync="folder.name" :update-fn="update"></editable-text-field>
            </div>
            <div class="folder-menu">
                <button type="button" class="btn" data-toggle="dropdown"><i class="fa fa-caret-down"></i></button>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="list-unstyled list-folder-actions">
                        <li><a @click.prevent="deleteFolder"><i class="fa fa-trash-o"></i> Delete Permanently</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="files-list">
            <project-file v-for="file in folder.files" :file.sync="file"></project-file>
        </div>
        <add-project-file :folder.sync="folder"></add-project-file>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {}
        },
        watch: {
            index(newIndex){
                this.folder.position = newIndex;
                this.update();
            }
        },
        props: ['folder', 'index'],
        methods: {
            flushAndAddToRequestsQueue(xhr){
                for (var i = 0; i < this.folder.requests_queue.length; i++) {
                    this.folder.requests_queue.shift().abort();
                }
                this.folder.requests_queue.push(xhr);
            },
            update(){
                this.$http.put(`/projects/${ this.folder.project_id }/folders/${ this.folder.id }`, this.folder, {
                    before(xhr) {
                        this.flushAndAddToRequestsQueue(xhr);
                    }
                }).then((res) => {
                    console.log('updated folder');
                }, (res) => {
                    console.log('error updating folder');
                    console.log(res);
                });
            },
            deleteFolder(){
                this.$http.delete(`/projects/${ this.folder.project_id }/folders/${ this.folder.id }`, {
                    before(xhr) {
                        this.flushAndAddToRequestsQueue(xhr);
                    }
                }).then((res) => {
                    vueGlobalEventBus.$emit('deleted-folder', this.folder);
                }, (res) => {
                    console.log('error deleting project folder');
                });
            }
        },
        ready(){
            this.folder.requests_queue = [];
        }
    }
</script>