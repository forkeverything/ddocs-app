<template>
    <div class="comments-thread">
        <ul class="list-comments list-unstyled" ref="list">
            <li class="single-comment" v-for="comment in comments">
                <h5 class="sender">{{ comment.sender.name }}</h5>
                <p class="body">
                    {{ comment.body }}
                </p>
            </li>
        </ul>
        <new-comment-field :project-id="projectId"
                           :comments="comments"
                           :subject-type="subjectType"
                           :subject-id="subjectId"
                           :subject-hash="subjectHash"
                           @add-new-comment="addNewComment"
        ></new-comment-field>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                comments: [],
            }
        },
        watch: {
            comments() {
                this.scrollToBottom();
            }
        },
        computed: {
        },
        props: ['project-id', 'subject-type', 'subject-id', 'subject-hash'],
        methods: {
            addNewComment(newComment){
                this.comments.push(newComment);
            },
            fetchComments(){

                let url = '/comments/';

                if(this.subjectType === 'App\\\\ProjectFile') {
                    url += `project_file/${ this.subjectId }`;
                } else if(this.subjectType === 'App\\\\FileRequest') {
                    url += `file_request/${ this.subjectHash }`;
                }

                this.$http.get(url, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((res) => {
                    this.comments = res.json();
                }, (res) => {
                    console.log('error fetching comments');
                    console.log(res);
                });
            },
            scrollToBottom(){
                this.$refs.list.scrollTop = this.$refs.list.scrollHeight;
            }
        },
        created() {
            vueGlobalEventBus.$on('showing-comments', this.scrollToBottom);
        },
        mounted() {
            this.fetchComments();
            this.$nextTick(() => {
                this.scrollToBottom();
            });
        },
        beforeDestroy(){
            vueGlobalEventBus.$off('showing-comments', this.scrollToBottom);
        }
    }
</script>