<template>
    <div class="project-file" :data-id="file.id" @click="viewFile">
        <div class="file-name truncate">
                {{ file.name }}
        </div>
    </div>
</template>
<script>
    const Vue = require('vue');
    export default {
        data: function () {
            return {
                request: ''
            }
        },
        watch: {
            'file.project_folder_id'(newFolderId) {
                this.update({project_folder_id: newFolderId});
            },
            'file.position'(newPosition) {
                this.update({position: newPosition});
            },
            index() {
                this.updateFileModel({ position: this.index });
            }
        },
        props: ['index', 'file', 'projectId', 'folder-index'],
        methods: {
            updateFileModel(file){
                this.$store.commit('project/UPDATE_FILE', {
                    folderIndex: this.folderIndex,
                    fileIndex: this.index,
                    file
                });
            },
            setNewRequest(xhr){
                if(this.request) RequestsMonitor.abortRequest(this.request);
                this.request = xhr;
            },
            update(updatedProperties){
                updatedProperties['id'] = this.file.id;
                this.$store.commit('project/SAVE_CHANGES', {
                    type: 'files',
                    model: updatedProperties
                });
            },
            viewFile(){
                vueGlobalEventBus.$emit('view-project-file', this.file);
            }
        },
        created(){

        },
        mounted() {
            if(this.file.position !== this.index) this.updateFileModel({ position: this.index });
        },
        beforeDestroy(){
        }
    };
</script>