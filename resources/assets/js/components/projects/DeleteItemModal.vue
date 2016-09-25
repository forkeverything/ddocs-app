<template>
<div class="delete-project-item-modal">
    <div class="modal fade" tabindex="-1" role="dialog" v-el:modal>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Permanently Delete Project Item</h4>
                </div>
                <div class="modal-body">
                    <p>
                        Are you sure you want to delete project item '{{ item.name }}'. All sub-items and file request links will be removed but the files will not be deleted.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-space btn-sm" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger btn-sm" @click="deleteItem">
                        {{ submitText }}
                    </button>
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
            ajaxReady: true,
            item: ''
        }
    },
    computed: {
        submitText: function () {
            if (!this.ajaxReady) return 'Processing...';
            return 'Delete';
        }
    },
    methods: {
        deleteItem() {
            if(! this.ajaxReady) return;
            this.ajaxReady = false;
            let type = '';
            if(this.item.type === 'App\\ProjectCategory') type = 'category';
            if(this.item.type === 'App\\ProjectFile') type = 'file';
            this.$http.delete(`/projects/${this.item.project_id}/item/${type}/${this.item.id}`).then((response) => {
                vueGlobalEventBus.$emit('set-project', response.json());
                $(this.$els.modal).modal('hide');
                this.item = '';
                this.ajaxReady = true;
            }, (response) => {
                console.log(response);
                console.log("couldn't delete project item");
            });
        }
    },
    ready() {
        vueGlobalEventBus.$on('delete-project-item', (item) => {
            this.item = item;
            $(this.$els.modal).modal('show')
        });
    }
}
</script>