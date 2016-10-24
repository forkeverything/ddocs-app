<template>
<div class="new-comment-field">
    <form @submit.prevent="addComment">
    <textarea class="form-control" v-model="body" rows="1" @keydown.enter="hitEnter($event)" placeholder="Write a comment..."></textarea>
    </form>
</div>
</template>
<script>
export default {
    data: function(){
        return {
            body: ''
        }
    },
    methods: {
        hitEnter(e){
            if(! e.shiftKey) {
                this.addComment();
                e.preventDefault();
                e.stopPropagation();
            }
        },
        addComment: _.throttle(function() {
            this.$emit('add-comment', this.body);
            this.body = '';
        }, 500)
    }
}
</script>