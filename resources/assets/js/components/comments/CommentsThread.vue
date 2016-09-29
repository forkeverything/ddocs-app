<template>
    <div class="comments-thread">
        <ul class="list-comments list-unstyled" v-el:list>
            <li class="single-comment" v-for="comment in comments">
                <h5 class="sender">{{ comment.sender.name }}</h5>
                <p class="body">
                    {{ comment.body }}
                </p>
            </li>
        </ul>
        <new-comment-field :project-id="projectId" :comments.sync="comments" :subject-type="subjectType"
                           :subject-id="subjectId"></new-comment-field>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {}
        },
        watch: {
            comments() {
                this.scrollToBottom();
            }
        },
        computed: {
        },
        props: ['project-id', 'comments', 'subject-type', 'subject-id'],
        methods: {
            scrollToBottom(){
                this.$els.list.scrollTop = this.$els.list.scrollHeight;
                console.log(this.$els.list.scrollHeight);
            }
        },
        ready(){
            this.scrollToBottom();
            vueGlobalEventBus.$on('showing-comments', () => {
                this.scrollToBottom();
            });
        }
    }
</script>