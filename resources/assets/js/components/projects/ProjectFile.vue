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
                request: '',
                requestsQueue: []
            }
        },
        watch: {
            file: {
                handler(newVal) {
                    this.update();
                },
                deep: true
            },
            index() {
                this.updateFileModel({ position: this.index });
            }
        },
        props: ['index', 'file', 'projectId', 'folder-index'],
        methods: {
            updateFileModel(file){
                this.$store.commit('updateProjectFile', {
                    folderIndex: this.folderIndex,
                    fileIndex: this.index,
                    file
                });
            },
            setNewRequest(xhr){
                if(this.request) RequestsMonitor.abortRequest(this.request);
                this.request = xhr;
            },
            update(){
                this.$http.put(`/api/projects/${ this.projectId }/files/${ this.file.id }`, this.file, {
                    before(xhr) {
                        this.setNewRequest(xhr);
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((res) => {
                    console.log('updated file');
                }, (res) => {
                    console.log('error updating file');
                    console.log(res);
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