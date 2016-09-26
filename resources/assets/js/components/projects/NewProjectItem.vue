<template>
<li id="new-project-item">
    <input type="text"
           v-model="name"
           v-el:input
           @blur="processForm"
           v-show="newField"
           :class="newField"
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
    watch: {
        newField() {
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

            this.$http.post(`/projects/${ this.parent.project_id }/${ this.newField }`, {
                name: this.name,
                parent_id: this.parent.id,
                parent_type: this.parent.type,
                position: 0,
                project_id: this.parent.project_id
            }).then((res) => {
                this.hideField();
                this.name = '';
                this.parent.items.unshift(res.json());
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