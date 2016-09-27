<template>
<div class="folder-body">
    <div class="folder-header">
        <h5>{{ folder.name }}</h5>
    </div>
    <div class="files-list">
        <project-file v-for="file in folder.files" :file.sync="file" ></project-file>
    </div>
    <add-project-file :folder.sync="folder"></add-project-file>
</div>
</template>
<script>
export default {
    data: function(){
        return {

        }
    },
    watch: {
        index(newIndex){
            console.log('update position: ' + newIndex);
        }
    },
    props: ['folder', 'index'],
    methods: {

    }
}
</script>