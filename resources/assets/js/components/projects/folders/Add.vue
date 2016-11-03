<template>
    <form @submit.prevent="createFolder">
        <input ref="input-name"
               type="text"
               placeholder="Create folder..."
               class="input-name form-control"
               v-model="name"
               :class="{
                'filled': name
               }"
        >
        <div class="text-right" v-show="name">
            <button type="submit" class="btn btn-sm btn-success">Create</button>
        </div>
    </form>
</template>
<script>
    export default {
        data: function () {
            return {
                ajaxReady: true,
                name: ''
            }
        },
        computed: {
            project() {
                return this.$store.state.project.data;
            }
        },
        props: ['folders'],
        methods: {
            adjustScroll() {
                let $container = $('.board-wrap');
                let $content = $('#project-board');
                let maxScroll = $content.width() - $container.width();
                if(maxScroll) $container.scrollLeft(maxScroll);
            },
            createFolder() {
                if (!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.post(`/api/projects/${ this.project.id }/folders`, {
                    name: this.name,
                    position: this.folders.length
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.$store.commit('project/INSERT_FOLDER', {
                        index: this.folders.length,
                        folder: response.json()
                    });
                    this.name = '';
                    this.ajaxReady = true;
                    this.$nextTick(() => {
                        $(this.$refs.inputName).focus();
                        this.adjustScroll();
                        vueGlobalEventBus.$emit('init-drag');
                    });

                }, (response) => {
                    // error
                    console.log('error creating folder');
                    console.log(response);
                    this.ajaxReady = true;
                })
            }
        }
    }
</script>