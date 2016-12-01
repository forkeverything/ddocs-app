<template>
<div class="new-comment-field">
    <form @submit.prevent="addComment" v-if="authenticatedUser">
    <textarea class="form-control" v-model="body" rows="1" @keydown.enter="hitEnter($event)" placeholder="Write a comment..." :disabled="saving"></textarea>
    </form>
    <input v-if="! authenticatedUser" type="text" class="form-control" disabled placeholder="Must be signed in to comment.">
</div>
</template>
<script>
export default {
    data: function(){
        return {
            body: ''
        }
    },
    computed: {
        authenticatedUser(){
            return this.$store.state.authenticatedUser;
        }
    },
    props: ['saving'],
    watch: {
        saving(newVal, oldVal) {
            if(newVal === false && oldVal === true) this.body = '';
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
        }, 500),
}
}
</script>