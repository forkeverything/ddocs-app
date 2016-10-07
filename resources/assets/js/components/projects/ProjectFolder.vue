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
            <project-file v-for="(file, fileIndex) in folder.files"
                          :key="file.id"
                          :project-id="folder.project_id"
                          :index="fileIndex"
                          :file="file"
                          :folder-index="index"
            >
            </project-file>
        </div>
        <add-project-file :folder="folder" :folder-index="index"></add-project-file>
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
            project() {
                return this.$store.state.project;
            },
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
                this.updateFolderModel({position: this.index});
            }
        },
        methods: {
            updateFolderModel(folder){
                this.$store.commit('updateProjectFolder', {
                    index: this.index,
                    folder
                });
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
                this.$http.delete(`/api/projects/${ this.folder.project_id }/folders/${ this.folder.id }`, {
                    before(xhr) {
                        this.setNewRequest(xhr);
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((res) => {
                    this.$store.commit('removeProjectFolder', this.index);
                }, (res) => {
                    console.log('error deleting project folder');
                });
            },
            handleDroppingFile(el, target, source, sibling){
                if (parseInt(source.dataset.id) !== this.folder.id) return;
                let targetFile = _.find(this.folder.files, {id: parseInt(el.dataset.id)});
                let targetFileIndex = _.indexOf(this.folder.files, targetFile);
                this.$store.commit('removeProjectFile', {
                    folderIndex: this.index,
                    fileIndex: targetFileIndex
                });
                this.$nextTick(() => {
                    let targetFolderIndex = _.indexOf(this.project.folders, _.find(this.project.folders, {id: parseInt(target.dataset.id)}));
                    let siblingIndex = this.project.folders[targetFolderIndex].files.length;
                    if (sibling) {
                        let siblingFile = _.find(this.project.folders[targetFolderIndex].files, {id: parseInt(sibling.dataset.id)});
                        siblingIndex = _.indexOf(this.project.folders[targetFolderIndex].files, siblingFile);
                    }
                    let differentParent = source.dataset.id !== target.dataset.id;
                    let newIndex = (targetFileIndex >= siblingIndex || differentParent) ? siblingIndex : siblingIndex - 1;
                    this.$store.commit('insertProjectFile', {
                        folderIndex: targetFolderIndex,
                        fileIndex: newIndex,
                        file: targetFile
                    });
                    this.$nextTick(() => {
                        targetFile.project_folder_id = parseInt(target.dataset.id);
                    });
                });
            }
        },
        created(){
            vueGlobalEventBus.$on('dropped-file', this.handleDroppingFile);
        },
        mounted() {
            if(this.folder.position !== this.index) {
                this.updateFolderModel({position: this.index});
                this.$nextTick(this.update);
            }
        },
        beforeDestroy(){
            vueGlobalEventBus.$off('dropped-file');
        }
    }
</script>