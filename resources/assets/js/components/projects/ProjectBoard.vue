<template>
    <div id="project-board" :class="{ dragging: dragging }">
        <template v-for="(folder, index) in project.folders">
            <div class="project-folder folder-wrap" :data-id="folder.id">
                <project-folder :index="index"
                                :folder="folder"
                                @update-folder-position="updateFolderPosition"
                                @remove-file="removeFile"
                                @insert-file="insertFile"
                                @update-file="updateFile"
                >
                </project-folder>
            </div>
        </template>
        <div class="add-folder folder-wrap">
            <form-add-project-folder :project-id="project.id" :folders="project.folders"
                                     @add-folder="addFolder"></form-add-project-folder>
        </div>
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
        props: ['project'],
        methods: {
            updateFile(folderIndex, fileIndex, fileObj) {
                for (let prop in fileObj) {
                    if( fileObj.hasOwnProperty( prop ) ) {
                        this.project.folders[folderIndex].files[fileIndex].prop = fileObj[prop];
                    }
                }
            },
            addFolder(folder){
                this.project.folders.push(folder);
            },
            insertFile(folderIndex, fileIndex, fileModel) {
                this.project.folders[folderIndex].files.splice(fileIndex, 0, fileModel);
            },
            removeFile(folderIndex, fileIndex) {
                this.project.folders[folderIndex].splice(fileIndex, 1);
            },
            updateFolderPosition(index){
                this.project.folders[index].position = index;
            },
            updateFolderIndexes(el, target, source, sibling){
                let targetFolder = _.find(this.project.folders, {id: parseInt(el.dataset.id)});
                let siblingFolder = _.find(this.project.folders, {id: parseInt(sibling.dataset.id)});
                let currentIndex = _.indexOf(this.project.folders, targetFolder);
                let siblingIndex = siblingFolder ? _.indexOf(this.project.folders, siblingFolder) : this.project.folders.length;
                this.project.folders.splice(currentIndex, 1);
                let newIndex = currentIndex > siblingIndex ? siblingIndex : siblingIndex - 1;
                this.project.folders.splice(newIndex, 0, targetFolder);
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
            vueGlobalEventBus.$on('deleted-folder', (folder) => {
                this.project.folders.splice(_.indexOf(this.project.folders, folder), 1);
            });
        },
        mounted() {
            this.$nextTick(() => {
                this.initFolderDrag();
                this.initFileDrag();
            });
        },
        beforeDestroy(){
            vueGlobalEventBus.$off('deleted-folder');
        }
    }
</script>