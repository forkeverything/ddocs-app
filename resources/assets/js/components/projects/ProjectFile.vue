<template>
    <div class="project-file" :data-id="file.id" @click.prevent="viewFile">
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
            index(newIndex) {
                this.file.position = newIndex;
            }
        },
        props: ['index', 'file', 'projectId'],
        methods: {
            setNewRequest(xhr){
                if(this.request) this.request.abort();
                this.request = xhr;
            },
            update(){
                this.$http.put(`/projects/${ this.projectId }/files/${ this.file.id }`, this.file, {
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
            vueGlobalEventBus.$on('update-file-folder', (file, folderId) => {
                if (file.id !== this.file.id) return;
                this.$emit('update-file', this.index, {project_folder_id: folderId});
            });

            vueGlobalEventBus.$on(`update-file-${ this.file.id }`, this.update);
        },
        mounted() {
            if(this.file.position !== this.index) {
                this.$emit('update-file', this.index, {position: this.index});
                this.$nextTick(this.update);
            }
        },
        beforeDestroy(){
            vueGlobalEventBus.$off('update-file-folder');
            vueGlobalEventBus.$off(`update-file-${ this.file.id }`);
        }
    };
</script>