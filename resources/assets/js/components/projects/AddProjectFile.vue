<template>
    <div class="add-project-file">
        <a class="placeholder text-muted" @click.prevent="toggleVisible" v-show="! visible">
            Add file...
        </a>
        <form @submit.prevent="addFile" v-show="visible">
            <input type="text" ref="input" class="form-control" @blur="clearInput" v-model="name">
            <button type="submit" class="btn btn-info">Add</button>
        </form>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                ajaxReady: true,
                visible: false,
                name: ''
            }
        },
        props: ['folder'],
        methods: {
            toggleVisible() {
                this.visible = !this.visible;
                if(this.visible) {
                    this.$nextTick(() => {
                        $(this.$refs.input).focus();
                    })
                }
            },
            clearInput() {
                this.name = '';
                this.visible = false;
            },
            addFile(){
                if(! this.name) return this.toggleVisible();
                if(! this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.post(`/projects/${ this.folder.project_id }/folders/${ this.folder.id }/files`, {
                    name: this.name,
                    position: this.folder.files.length
                }).then((res) => {

                    this.$emit('add-file', res.json());

                    this.ajaxReady = true;
                    this.name = '';
                    this.$nextTick(() => {
                        $(this.$refs.input).focus();
                    });
                }, (res) => {
                    console.log(res);
                    console.log('error adding file');
                    this.name = '';
                    this.ajaxReady = true;
                    this.visible = false;
                });

            }
        }
    }
</script>