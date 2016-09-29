<template>
<div class="new-comment-field">
    <form @submit.prevent="addComment">
    <textarea class="form-control" v-model="body" rows="1" @keydown.enter="hitEnter($event)"></textarea>
    </form>
</div>
</template>
<script>
export default {
    data: function(){
        return {
            ajaxReady: true,
            body: ''
        }
    },
    props: ['file-request-hash', 'project-id', 'comments', 'subject-id', 'subject-type'],
    methods: {
        hitEnter(e){
            if(! e.shiftKey) {
                this.addComment();
                e.preventDefault();
                e.stopPropagation();
            }
        },
        makeUrl(){
            console.log('bar');
            if(this.subjectType === 'App\\\\ProjectFile') {
                return `/projects/${ this.projectId }/files/${ this.subjectId }`;
            } else if(this.subjectType === 'App\\\\FileRequest') {
                return `/fr/${ this.fileRequestHash }/comments`;
            }
        },
        addComment(){
            if(!this.ajaxReady) return;
            this.ajaxReady = false;

            this.$http.post(this.makeUrl(), {
                body: this.body
            }).then((response) => {
                // success
                this.comments.push(response.json());
                this.ajaxReady = true;
                this.$nextTick(() => {
                    this.body = '';
                });
            }, (response) => {
                // error
                console.log('error adding comment.');
                console.log(response);
                this.ajaxReady = true;
            });
        }
    }
}
</script>