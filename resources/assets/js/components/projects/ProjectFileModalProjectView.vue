<template>
    <div class="project">
        <span class="small text-muted">Only team members in the project can see this section.</span>
        <div class="description">
            <h5>Description</h5>
            <editable-text-area v-model="file.description"
                                :allow-null="true"
                                :placeholder="'Details about the file...'"
            ></editable-text-area>
        </div>
        <div class="comments">
            <h5>Team Comments</h5>
            <comments-thread :comments="comments" @add-comment="addComment" :loading="loadingComments"></comments-thread>
        </div>
    </div>
</template>
<script>
    const hasComments = require('../../mixins/HasComments');
    export default {
        data: function () {
            return {}
        },
        props: ['file'],
        computed: {
            commentsUrl() {
                return `/api/comments/project_file/${this.file.id}`
            }
        },
        watch: {
            file(newFile) {
                if (newFile) this.fetchComments();
            }
        },
        methods: {},
        mixins: [hasComments]
    };
</script>