<template>
    <div class="comments-thread">
        <rectangle-loader :loading="loading" size="small"></rectangle-loader>
        <ul class="list-comments list-unstyled" ref="list">
            <li class="single-comment" v-for="comment in comments">
                <div class="top">
                    <span class="sender">{{ comment.sender.name }}</span> <span class="sent">{{ comment.created_at | diffHuman }}</span>
                </div>
                <p class="body" v-html="comment.body"></p>
            </li>
        </ul>
        <new-comment-field @add-comment="addComment" :saving="saving"></new-comment-field>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {

            }
        },
        watch: {
            comments() {
                this.$nextTick(this.scrollToBottom);
            }
        },
        computed: {},
        props: ['comments', 'loading', 'saving'],
        methods: {
            addComment(body){
                this.$emit('add-comment', body);
            },
            scrollToBottom(){
                this.$refs.list.scrollTop = this.$refs.list.scrollHeight;
            }
        },
        mounted() {
            this.$nextTick(this.scrollToBottom);
        }
    }
</script>