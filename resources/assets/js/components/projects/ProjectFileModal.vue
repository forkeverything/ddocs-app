<template>
<div id="project-modal">
    <div class="modal" tabindex="-1" role="dialog" v-el:modal>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                        <h4 class="file-name"><i class="fa fa-file-o"></i> <span class="name-wrap"><editable-text-field :value.sync="file.name"></editable-text-field></span></h4>
                    <div class="description">
                        <h5>Description</h5>
                        <editable-text-area :value.sync="file.description" :allow-null="true" :placeholder="'Details about the file...'"></editable-text-area>
                    </div>
                    <div class="comments">
                        <h5>Comments</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>
<script>
export default {
    data: function(){
        return {
            file: ''
        }
    },
    methods: {
        hide(){
            $(this.$els.modal).modal('hide');
        }
    },
    ready() {
        vueGlobalEventBus.$on('view-project-file', (file) => {
            this.file = file;
            this.$nextTick(() => {
                $(this.$els.modal).modal('show')
            });
        })
    }
}
</script>