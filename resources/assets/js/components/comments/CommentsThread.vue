<template>
    <div class="comments-thread">
        <rectangle-loader :loading="loading" size="small"></rectangle-loader>
        <ul class="list-comments list-unstyled" ref="list">
            <li class="single-comment loader" v-if="loading">

            </li>
            <li class="single-comment" v-for="comment in comments">
                <h5 class="sender">{{ comment.sender.name }}</h5>
                <p class="body">
                    {{ comment.body }}
                </p>
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