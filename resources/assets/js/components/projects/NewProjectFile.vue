<template>
<li id="new-project-file">
    <input type="text"
           v-model="name"
           v-el:input
           @blur="processForm"
           v-show="visible"
    >
</li>
</template>
<script>
export default {
    data: function(){
        return {
            ajaxReady: true,
            name: ''
        }
    },
    props: ['parent', 'new-field'],
    computed: {
        visible() {
            return this.newField === 'file';
        }
    },
    watch: {
        visible() {
            $(this.$els.input).focus();
        }
    },
    methods: {
        hideField() {
            this.newField = '';
        },
        processForm(){
            if(! this.name) return this.hideField();

            if (!this.ajaxReady) return;
            this.ajaxReady = false;

            this.$http.post(`/projects/${ this.projectId }/files`, {
                name: this.name,
                parent_id: this.parent.id,
                parent_type: this.parent.type,
                position: 0,
                project_id: this.parent.project_id
            }).then((res) => {
                this.parent.items.unshift(res.json());
                this.name = '';
                this.hideField();
                this.ajaxReady = true;
            }, (res)=> {
                console.log(res);
                console.log('error adding item');
                this.ajaxReady = true;
            });
        }
    }
}
</script>