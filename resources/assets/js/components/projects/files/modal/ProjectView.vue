<template>
    <div class="project-view">
        <p class="text-muted small"><i class="fa fa-eye"></i> Team members in project</p>
        <div class="due-date">
            <p class="text-muted">Due Date</p>
            <br>
            <date-picker v-model="file.due"
                         :formatted="true"
                         :button-only="true"
            ></date-picker>
        </div>
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
    const hasComments = require('../../../../mixins/HasComments');
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