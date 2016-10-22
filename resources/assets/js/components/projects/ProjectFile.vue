<template>
    <div class="project-file" :data-id="file.id" @click="viewFile">
        <div class="info-icons">
            <span class="attached"
                  :class="{
                    'is-attached': file.attached
                  }"
            >
                <i class="fa fa-link"></i>
            </span>
        </div>
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
                this.save({project_folder_id: newFolderId});
            },
            'file.position'(newPosition) {
                this.save({position: newPosition});
            },
            index() {
                this.updateFileModel({ position: this.index });
            },
            'file.name'(newName) {
                this.save({name: newName});
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
            save(updatedProperties){
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
            vueGlobalEventBus.$on('update-project-file', (file) => {
                if(file.id !== this.file.id) return;
                this.updateFileModel(file);
            });
        },
        mounted() {
            if(this.file.position !== this.index) this.updateFileModel({ position: this.index });
        },
        beforeDestroy(){
            vueGlobalEventBus.$off('update-project-file');
        }
    };
</script>