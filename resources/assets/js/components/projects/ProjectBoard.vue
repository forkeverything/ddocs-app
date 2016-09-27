<template>
<div id="project-board">
    <template v-for="folder in project.folders">
        <div class="project-folder folder-wrap">
            <project-folder :folder.sync="folder"></project-folder>
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

        }
    },
    props: ['project'],
    methods: {

    }
}
</script>