<template>
    <div class="project-folder-body">
        <div class="folder-header">
            <div class="folder-name">
                <editable-text-field v-model="folder.name" :update-fn="update" :clipped="true"></editable-text-field>
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
            <project-file v-for="(file, index) in folder.files" :key="file.id" :project-id="folder.project_id" :index="index" :file="file" @update-file="updateFile"></project-file>
        </div>
        <add-project-file :folder="folder" @add-file="addFile"></add-project-file>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                request: '',
                requestsQueue: []
            }
        },
        computed: {
            noFiles() {
                return this.folder.files.length === 0;
            }
        },
        props: ['folder', 'index'],
        watch: {
            folder: {
                handler(newVal) {
                    this.update();
                },
                deep: true
            },
            index(newIndex){
                this.$emit('update-folder-position', newIndex);
            }
        },
        methods: {
            addFile(fileModel) {
                this.$emit('insert-file', this.index, this.folder.files.length, fileModel);
            },
            updateFile(fileIndex, fileObj){
                this.$emit('update-file', this.index, fileIndex, fileObj);
            },
            setNewRequest(xhr){
                if(this.request) RequestsMonitor.abortRequest(this.request);
                this.request = xhr;
            },
            update(){
                this.$http.put(`/api/projects/${ this.folder.project_id }/folders/${ this.folder.id }`, this.folder, {
                    before(xhr) {
                        this.setNewRequest(xhr);
                        RequestsMonitor.pushOntoQueue(xhr);
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
                        this.setNewRequest(xhr);
                        RequestsMonitor.pushOntoQueue(xhr);
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
                this.$emit('remove-file', this.index, targetFileIndex);
                this.$nextTick(() => vueGlobalEventBus.$emit('insert-file', el, target, source, sibling, targetFile, targetFileIndex));
            },
            handleInsertingFile(el, target, source, sibling, targetFile, targetFileIndex) {
                if (parseInt(target.dataset.id) !== this.folder.id) return;     // Make sure we're in the right folder
                let siblingIndex = this.folder.files.length;
                if (sibling) {
                    let siblingFile = _.find(this.folder.files, {id: parseInt(sibling.dataset.id)});
                    siblingIndex = _.indexOf(this.folder.files, siblingFile);
                }
                let differentParent = source.dataset.id === target.dataset.id;
                let newIndex = (targetFileIndex >= siblingIndex || differentParent) ? siblingIndex : siblingIndex - 1;
                this.$emit('insert-file', this.index, newIndex, targetFile);
                this.$nextTick(() => vueGlobalEventBus.$emit('update-file-folder', targetFile, parseInt(target.dataset.id)));
            }
        },
        created(){
            vueGlobalEventBus.$on('dropped-file', this.handleDroppingFile);
            vueGlobalEventBus.$on('insert-file', this.handleInsertingFile)
        },
        mounted() {
            if(this.folder.position !== this.index) {
                this.$emit('update-folder-position', this.index);
            }
        },
        beforeDestroy(){
            vueGlobalEventBus.$off('dropped-file');
            vueGlobalEventBus.$off('insert-file');
        }
    }
</script>