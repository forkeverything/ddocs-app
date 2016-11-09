<template>
    <div id="project-single">
        <rectangle-loader :loading="initializing" size="large"></rectangle-loader>
        <div id="project-main-actions" class="dropdown">
            <button type="button" class="dropdown-toggle btn btn-text" data-toggle="dropdown">•••</button>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a href="#" @click.prevent="toggleEditName">Edit Name</a>
                </li>
                <li>
                    <a href="#" @click.prevent="deleteProject">Delete Permanently</a>
                </li>
            </ul>
        </div>
        <div id="project-info" class="container-fluid">
            <form id="form-project-name" @submit.prevent="updateName" v-show="editingName">
                <label for="">Name</label>
                <div class="form-group">
                    <input type="text" v-model="newName" class="form-control" ref="nameInput">
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-info btn-sm btn-space">Save</button>
                    <button type="button" class="btn btn-sm btn-default" @click="toggleEditName">Cancel</button>
                </div>
            </form>
            <div id="project-description">
                <editable-text-area v-model="project.description" @on-change="updateDescription" allow-null="true"
                                    placeholder="Write project description...">
                    {{ project.description }}
                </editable-text-area>
            </div>
            <project-members></project-members>
        </div>
        <div class="board-wrap">
            <div id="project-board" :class="{ dragging: dragging }">
                <template v-for="(folder, index) in project.folders">
                    <div class="project-folder folder-wrap" :data-id="folder.id" :key="folder.id">
                        <project-folder :index="index"
                                        :folder="folder"
                        >
                        </project-folder>
                    </div>
                </template>
                <div class="add-folder folder-wrap">
                    <form-add-project-folder :folders="project.folders"
                    ></form-add-project-folder>
                </div>
            </div>
        </div>
        <project-file-modal></project-file-modal>
    </div>
</template>
<script>
    export default {
        name: 'ProjectBoard',
        data: function () {
            return {
                ajaxReady: true,
                initializing: true,
                dragging: false,
                folderDrake: '',
                autoScroll: '',
                fileDrake: '',
                editingName: false,
                newName: ''
            }
        },
        props: [],
        computed: {
            project(){
                return this.$store.state.project.data;
            }
        },
        watch: {
            editingName(editing) {
                this.newName = this.project.name;
                if(editing) this.$nextTick(() => $(this.$refs.nameInput).focus());
            }
        },
        methods: {
            toggleEditName() {
                this.editingName = !this.editingName;
            },
            updateDescription(newDescription) {
                this.save({
                    description: newDescription
                });
            },
            updateName() {
                this.save({
                    name: this.newName
                });
                this.project.name = this.newName;
                this.$store.commit('setTitle', `${ this.newName }<i class="fa fa-industry"></i>`);
                this.editingName = false;
            },
            save(updatedProperties) {
                this.$store.commit('project/SAVE_CHANGES', {
                    type: 'project',
                    model: updatedProperties
                });
            },
            initDrag() {
                this.initFolderDrag();
                this.initFileDrag();
            },
            fetchProject(){
                this.$http.get(`/api/projects/${ this.$route.params.project_id }`, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.$store.commit('project/SET', response.json());
                    this.$nextTick(() => {
                        this.$store.commit('setTitle', `${ this.project.name }<i class="fa fa-industry"></i>`);
                        this.initDrag();
                        this.initializing = false;
                    });

                }, (response) => {
                    // error
                    if (response.status === 403) this.$router.push('/projects');
                    console.log('error fetching project');
                });
            },
            updateFolderIndexes(el, target, source, sibling){
                let targetFolder = _.find(this.project.folders, {id: parseInt(el.dataset.id)});
                let siblingFolder = _.find(this.project.folders, {id: parseInt(sibling.dataset.id)});
                let currentIndex = _.indexOf(this.project.folders, targetFolder);
                let siblingIndex = siblingFolder ? _.indexOf(this.project.folders, siblingFolder) : this.project.folders.length;
                this.$store.commit('project/REMOVE_FOLDER', currentIndex);
                let newIndex = currentIndex > siblingIndex ? siblingIndex : siblingIndex - 1;
                this.$store.commit('project/INSERT_FOLDER', {
                    index: newIndex,
                    folder: targetFolder
                });
            },
            initFolderDrag(){
                // if we're re-initializing
                if (this.folderDrake) this.folderDrake.destroy();
                this.folderDrake = dragula([document.querySelector('#project-board')], {
                    moves: (el, source, handle, sibling) => {

                        let draggingProjectFile = false;

                        if (handle.classList.contains('project-file')) draggingProjectFile = true;

                        while (handle = handle.parentNode) {
                            if (handle.classList && handle.classList.contains('project-file')) {
                                draggingProjectFile = true;
                                break;
                            }
                        }

                        return !el.classList.contains('add-folder') && !draggingProjectFile;
                    },
                    accepts: (el, target, source, sibling) => {
                        return sibling;
                    },
                    direction: 'horizontal'
                });

                this.folderDrake.on('drop', (el, target, source, sibling) => {
                    this.folderDrake.cancel(true);
                    this.updateFolderIndexes(el, target, source, sibling);
                });

                // 'dragging' class
                this.folderDrake.on('drag', () => {
                    this.dragging = true;
                });

                this.folderDrake.on('cancel', () => {
                    this.dragging = false;
                });

                this.folderDrake.on('dragend', () => {
                    this.dragging = false;
                });

                if (this.autoScroll) this.autoScroll.destroy();
                this.autoScroll = autoScroll([document.querySelector('.board-wrap')], {
                    margin: 30,
                    pixels: 100,
                    scrollWhenOutside: true,
                    autoScroll: () => {
                        //Only scroll when the pointer is down, and there is a child being dragged.
                        return this.autoScroll.down && (this.folderDrake.dragging || this.fileDrake.dragging);
                    }
                });
            },
            initFileDrag(){
                // if we're re-initializing
                if (this.fileDrake) this.fileDrake.destroy();

                this.fileDrake = dragula([].slice.call(document.querySelectorAll('.files-list')), {
                    direction: 'vertical',
                    moves: (el, source, handle, sibling) => {
                        return true;
                    }
                });

                this.fileDrake.on('drop', (el, target, source, sibling) => {
                    this.fileDrake.cancel(true);
                    vueGlobalEventBus.$emit('dropped-file', el, target, source, sibling);
                });

                // 'dragging' class
                this.fileDrake.on('drag', () => {
                    this.dragging = true;
                });

                this.fileDrake.on('cancel', (el) => {
                    this.dragging = false;
                });

                this.fileDrake.on('dragend', (el) => {
                    this.dragging = false;
                });

            },
            deleteProject() {
                if (!this.ajaxReady) return;
                this.ajaxReady = false;
                this.$http.delete(`/api/projects/${ this.project.id }`, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((res) => {
                    router.push('/');
                }, (res) => {
                    console.log('error deleting project.');
                    this.ajaxReady = true;
                });
            }
        },
        created() {
            vueGlobalEventBus.$on('init-drag', this.initDrag);
        },
        mounted() {
            this.fetchProject();
        },
        beforeDestroy(){
            vueGlobalEventBus.$off('init-drag');
            this.$store.commit('project/SET', '');
        }
    }
</script>
