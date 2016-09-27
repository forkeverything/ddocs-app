<template>
<div id="project-board">
    <template v-for="(index, folder) in project.folders">
        <div class="project-folder folder-wrap" :data-id="folder.id">
            <project-folder :index="index" :folder.sync="folder"></project-folder>
        </div>
    </template>
    <div class="add-folder folder-wrap">
        <form-add-project-folder :project-id="project.id" :folders.sync="project.folders"></form-add-project-folder>
    </div>
</div>
</template>
<script>
export default {
    data: function(){
        return {
            drake: ''
        }
    },
    props: ['project'],
    methods: {
        updateIndexes(el, target, source, sibling){
            let targetFolder = _.find(this.project.folders, {id: parseInt(el.dataset.id)});
            let siblingFolder = _.find(this.project.folders, {id: parseInt(sibling.dataset.id)});
            let currentIndex = _.indexOf(this.project.folders, targetFolder);
            let siblingIndex = _.indexOf(this.project.folders, siblingFolder);
            this.project.folders.splice(currentIndex, 1);
            let newIndex = currentIndex > siblingIndex ? siblingIndex : siblingIndex - 1;
            this.project.folders.splice(newIndex, 0, targetFolder);
        },
        initFolderDrag(){
            // if we're re-initializing
            if (this.drake) this.drake.destroy();
            this.drake = dragula([document.querySelector('#project-board')], {
                moves: (el, source, handle, sibling) => {
                    return !el.classList.contains('add-folder');
                },
                accepts: (el, target, source, sibling) => {
                    return sibling;
                },
                direction: 'horizontal'
            });

            this.drake.on('drop', (el, target, source, sibling) => {
                this.updateIndexes(el, target, source,sibling);
                this.drake.cancel(true);
            });
        }
    },
    ready() {
        this.initFolderDrag();
    }
}
</script>