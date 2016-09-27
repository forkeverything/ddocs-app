<template>
    <div class="add-project-file">
        <a class="placeholder text-muted" @click.prevent="toggleVisible" v-show="! visible">
            Add file...
        </a>
        <form @submit.prevent="addFile" v-show="visible">
            <input type="text" v-el:input class="form-control" @blur="clearInput" v-model="name">
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
                        $(this.$els.input).focus();
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
                    name: this.name
                }).then((res) => {
                    this.folder.files.push(res.json());
                    this.ajaxReady = true;
                    this.name = '';
                    this.$nextTick(() => {
                        $(this.$els.input).focus();
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