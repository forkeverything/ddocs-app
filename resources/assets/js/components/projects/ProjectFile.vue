<template>
    <div class="project-file" :data-id="file.id" @click="viewFile">
        <pf-status-circle :project-file="file"></pf-status-circle>
        <div class="file-name truncate">
            <a v-if="latestUploadPath"
               :href=" awsURL + latestUploadPath"
               :alt="file.name + 'download link'"
               @click.stop=""
            >
                {{ file.name }}
            </a>
            <span v-if="! latestUploadPath">{{ file.name }}</span>
        </div>
        <div class="info-badges">
            <span class="attached badge" v-if="file.file_request">
                <i class="fa fa-link"></i>
            </span>
            <span class="uploads badge" v-if="file.meta.num_uploads">
                <i class="fa fa-upload"></i>{{ file.meta.num_uploads }}
            </span>
            <span class="due-date badge" v-if="file.due">
                <i class="fa fa-calendar"></i><smart-date :date="file.due"></smart-date>
            </span>
            <span class="weighting badge" v-if="file.weighting">
                <span class="icon">%</span>{{ file.weighting }}
            </span>
        </div>
    </div>
</template>
<script>
    const Vue = require('vue');
    export default {
        data: function () {
            return {
                awsURL: awsURL,
                request: ''
            }
        },
        computed: {
            latestUploadPath() {
                if(this.file.file_request && this.file.file_request.status === 'received') return this.file.file_request.latest_upload.path;
                if(this.file.latest_upload) return this.file.latest_upload.path;
                return false;
            }
        },
        watch: {
            index() {
                this.updateModel({ position: this.index });
            },
            'file.position'(newPosition) {
                this.save({position: newPosition});
            },
            'file.project_folder_id'(newFolderId) {
                this.save({project_folder_id: newFolderId});
            },
            'file.name'(newName) {
                this.save({name: newName});
            },
            'file.due'(newDueDate = null) {
                this.save({due: newDueDate});
            },
            'file.weighting'(weighting = null) {
                this.save({weighting: weighting});
            }
        },
        props: ['index', 'file', 'projectId', 'folder-index'],
        methods: {
            updateModel(file){
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
                this.updateModel(file);
            });
            vueGlobalEventBus.$on('delete-project-file', (id) => {
                if(id !== this.file.id) return;
                this.$store.commit('project/REMOVE_FILE', {
                    folderIndex: this.folderIndex,
                    fileIndex: this.index
                });
            });
        },
        mounted() {
            if(this.file.position !== this.index) this.updateModel({ position: this.index });
        },
        beforeDestroy(){
            vueGlobalEventBus.$off('update-project-file');
        }
    };
</script>