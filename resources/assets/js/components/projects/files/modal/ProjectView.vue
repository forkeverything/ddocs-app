<template>
    <div class="project-view">
        <div class="summary">
            <span class="visibility info">
                <i class="fa fa-eye"
                   data-toggle="tooltip" data-placement="bottom" title="Team members in project"
                ></i>
            </span>
            <div class="due-date info">
                <date-picker v-model="file.due"
                             :carbon="true"
                             :formatted="true"
                             :button-only="true"
                             :keep-button="true"
                             :placeholder="'Due date'"
                             :on-change="updateDate"
                ></date-picker>
            </div>
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
        methods: {
            updateDate(dueDate) {
                vueGlobalEventBus.$emit('update-project-file', {
                    id: this.file.id,
                    due: dueDate
                });
            }
        },
        mounted() {
            this.$nextTick(() => {
                $('[data-toggle="tooltip"]').tooltip()
            });
        },
        mixins: [hasComments]
    };
</script>