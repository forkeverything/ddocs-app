<template>
    <div id="project-single">
        <div id="project-info" class="container-fluid">
            <h3>
                <small>Project Files</small>
                <br>
                {{ project.name }}
            </h3>
            <p class="small text-muted" v-if="project.description">
                {{ project.description }}
            </p>
            <br>
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
                    <form-add-project-folder :project-id="project.id"
                                             :folders="project.folders"
                    ></form-add-project-folder>
                </div>
            </div>
        </div>
        <project-file-modal :project-id="project.id"></project-file-modal>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                dragging: false,
                folderDrake: '',
                autoScroll: '',
                fileDrake: ''
            }
        },
        props: [],
        computed: {
            project(){
                return this.$store.state.project.data;
            }
        },
        methods: {
            fetchProject(){
                this.$http.get(`/api/projects/${ this.$route.params.project_id }`, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.$store.commit('project/SET', response.json());
                    this.$nextTick(() => {
                        this.initFolderDrag();
                        this.initFileDrag();
                    });
                }, (response) => {
                    // error
                    console.log('Error fetching from: /projects/');
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

            }
        },
        created() {

        },
        mounted() {
            this.fetchProject();
        },
        beforeDestroy(){
            this.$store.commit('project/SET', '');
        }
    }
</script>