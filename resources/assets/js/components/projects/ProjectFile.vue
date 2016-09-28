<template>
    <div class="project-file" :data-id="file.id" @click.prevent="viewFile">
        <div class="file-name">
                {{ file.name }}
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {}
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
            flushAndAddToRequestsQueue(xhr){
                for (var i = 0; i < this.file.requests_queue.length; i++) {
                    this.file.requests_queue.shift().abort();
                }
                this.file.requests_queue.push(xhr);
            },
            update(){
                this.$http.put(`/projects/${ this.projectId }/files/${ this.file.id }`, this.file, {
                    before(xhr) {
                        this.flushAndAddToRequestsQueue(xhr);
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
        ready() {
            this.file.requests_queue = [];
            vueGlobalEventBus.$on('update-file-folder', (file, folderId) => {
                if (file.id !== this.file.id) return;
                this.$set('file.project_folder_id', folderId);
            });

            vueGlobalEventBus.$on(`update-file-${ this.file.id }`, this.update);

            if(this.file.position !== this.index) {
                this.file.position = this.index;
                this.update();
            }
        }
    };
</script>