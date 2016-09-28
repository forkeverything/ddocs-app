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
        <div class="files-list" :class="{ empty: noFiles }" :data-id="folder.id">
            <project-file v-for="(index, file) in folder.files" :project-id="folder.project_id" :index="index" :file.sync="file"></project-file>
        </div>
        <add-project-file :folder.sync="folder"></add-project-file>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {}
        },
        computed: {
            noFiles() {
                return this.folder.files.length === 0;
            }
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
            },
            handleDroppingFile(el, target, source, sibling){
                if (parseInt(source.dataset.id) !== this.folder.id) return;
                let targetFile = _.find(this.folder.files, {id: parseInt(el.dataset.id)});
                let targetFileIndex = _.indexOf(this.folder.files, targetFile);
                this.folder.files.splice(targetFileIndex, 1);
                el.remove();
                // TODO :: Find better way to do this. v-for isn't reactive after calling drake.cancel() on
                // element. Refreshing data means re-initializing all drag objects, not fun.
                this.$nextTick(() => {
                    vueGlobalEventBus.$emit('insert-file', el, target, source, sibling, targetFile, targetFileIndex);
                });
            },
            handleInsertingFile(el, target, source, sibling, targetFile, targetFileIndex) {
                // If we're in the right folder
                if (parseInt(target.dataset.id) !== this.folder.id) return;
                let siblingIndex = this.folder.files.length;
                if (sibling) {
                    let siblingFile = _.find(this.folder.files, {id: parseInt(sibling.dataset.id)});
                    siblingIndex = _.indexOf(this.folder.files, siblingFile);
                }
                let differentParent = source.dataset.id === target.dataset.id;
                let newIndex = (targetFileIndex >= siblingIndex || differentParent) ? siblingIndex : siblingIndex - 1;
                this.folder.files.splice(newIndex, 0, targetFile);
                this.$nextTick(() => {
                    vueGlobalEventBus.$emit('update-file-folder', targetFile, parseInt(target.dataset.id));
                });
            }
        },
        ready(){
            this.folder.requests_queue = [];
            vueGlobalEventBus.$on('dropped-file', (el, target, source, sibling) => {
                this.handleDroppingFile(el, target, source, sibling);
            });
            vueGlobalEventBus.$on('insert-file', (el, target, source, sibling, targetFile, targetFileIndex) => {
                this.handleInsertingFile(el, target, source, sibling, targetFile, targetFileIndex);
            });

            if(this.folder.position !== this.index) {
                this.folder.position = this.index;
                this.update();
            }
        }
    }
</script>