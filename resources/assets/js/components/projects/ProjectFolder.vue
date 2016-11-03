<template>
    <div class="project-folder-body">
        <div class="folder-header">
            <div class="folder-name">
                <editable-text-field :value="folder.name" @on-change="updateName" :clipped="true"></editable-text-field>
            </div>
            <div class="folder-menu">
                <button type="button" class="btn" data-toggle="dropdown"><i class="fa fa-caret-down"></i></button>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="folder-info">
                        <div class="num_files">
                            Files {{numReceivedFiles}} / {{ this.folder.files.length }}
                        </div>
                        <div class="folder-progress">
                            Progress <span v-if="weightingTotal">{{ weightingReceived }}% / {{ weightingTotal }}%</span><span v-if="! weightingTotal">--</span>
                        </div>
                    </div>
                    <ul class="list-unstyled list-folder-actions">
                        <li><a @click.prevent="deleteFolder"><i class="fa fa-trash-o"></i> Delete Permanently</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="files-list" :class="{ empty: emptyFolder }" :data-id="folder.id">
            <project-file v-for="(file, fileIndex) in folder.files"
                          :key="file.id"
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
                request: ''
            }
        },
        computed: {
            project() {
                return this.$store.state.project.data;
            },
            emptyFolder() {
                return this.folder.files.length === 0;
            },
            numReceivedFiles() {
                let numReceivedFiles = 0;
                for(let i = 0; i < this.folder.files.length; i ++ ) {
                    let file = this.folder.files[i];
                    if(file.file_request) {
                        if(file.file_request.status === 'received') numReceivedFiles ++;
                    } else {
                        if(file.meta.num_uploads) numReceivedFiles ++;
                    }
                }
                return numReceivedFiles;
            },
            weightingTotal() {
                let weightingTotal = 0;
                for(let i = 0; i < this.folder.files.length; i ++ ) {
                     weightingTotal += this.folder.files[i].weighting;
                }
                return weightingTotal;
            },
            weightingReceived() {
               let weightingReceived = 0;
                for(let i = 0; i < this.folder.files.length; i ++ ) {
                    let file = this.folder.files[i];
                    if(file.file_request) {
                        if(file.file_request.status === 'received')  weightingReceived += file.weighting;
                    } else {
                        if(file.meta.num_uploads)  weightingReceived += file.weighting;
                    }
                }
                return weightingReceived;
            }
        },
        props: ['folder', 'index'],
        watch: {
            'folder.position'(newPosition) {
                this.save({position: newPosition})
            },
            index(newIndex){
                this.updateModel({position: this.index});
            }
        },
        methods: {
            updateName(newName) {
                let folder = {
                    name: newName
                };
                this.updateModel(folder);
                this.save(folder);
            },
            updateModel(folder){
                this.$store.commit('project/UPDATE_FOLDER', {
                    index: this.index,
                    folder
                });
            },
            save(updatedProperties){
                updatedProperties['id'] = this.folder.id;
                this.$store.commit('project/SAVE_CHANGES', {
                    type: 'folders',
                    model: updatedProperties
                });
            },
            deleteFolder(){
                this.$http.delete(`/api/projects/${ this.folder.project_id }/folders/${ this.folder.id }`, {
                    before(xhr) {
                        if(this.request) RequestsMonitor.abortRequest(this.request);
                        this.request = xhr;
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((res) => {
                    this.$store.commit('project/REMOVE_FOLDER', this.index);
                }, (res) => {
                    console.log('error deleting project folder');
                });
            },
            handleFileDrop(el, target, source, sibling){
                if (parseInt(source.dataset.id) !== this.folder.id) return;
                let targetFile = _.find(this.folder.files, {id: parseInt(el.dataset.id)});
                let targetFileIndex = _.indexOf(this.folder.files, targetFile);
                this.$store.commit('project/REMOVE_FILE', {
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
                    let newIndex = (targetFileIndex >= siblingIndex || differentParent || ! sibling) ? siblingIndex : siblingIndex + 1;
                    this.$store.commit('project/INSERT_FILE', {
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
            vueGlobalEventBus.$on('dropped-file', this.handleFileDrop);
        },
        mounted() {
            if(this.folder.position !== this.index) this.updateModel({position: this.index});
        },
        beforeDestroy(){
            vueGlobalEventBus.$off('dropped-file');
        }
    }
</script>