<template>
     <div id="attach-fr-menu" class="dropdown-menu dropdown-menu-right">
            <h4>Attach File Request</h4>
            <p>Link an existing file request from a checklist to share with team members.</p>
            <input id="input-search-attach-fr"
                   class="form-control input-search"
                   type="text"
                   placeholder="Search..."
                   @keyup="searchTerm"
                   v-model="repoSearch"
                   :class="{
                                    'active': repoSearch && repoSearch.length > 0
                               }"
            >
            <div class="file-requests"
                 id="attachable-file-requests"
                 ref="results-container"
                 @scroll="scrollList"
            >
                <rectangle-loader :loading="loadingRepoResults" size="small"></rectangle-loader>
                <ul v-if="fileRequests"
                    id="list-attach-fr"
                    class="list-unstyled"
                >
                    <li v-for="fileRequest in fileRequests"
                        class="single-file-request clickable"
                        @click="attachFileRequest(fileRequest.hash)"
                    >
                        <div class="file-name">
                            {{ fileRequest.name }}
                        </div>
                        <div class="checklist">
                             <span class="recipients">
                                <i class="fa fa-users"></i> {{ fileRequest.checklist.meta.recipients.length }}
                            </span>
                            <span class="checklist-name">
                                {{ fileRequest.checklist.name }}
                            </span>
                        </div>
                    </li>
                </ul>
                <p v-if="! fileRequests"
                   class="empty-placeholder text-center">
                    <i class="fa fa-search"></i> No file requests found.
                    <br>
                    <span class="description">Search by file name, recipient email or checklist name.</span>
                </p>
            </div>
        </div>
</template>
<script>
    import FetchesFromEloquentRepository from "../../mixins/fetchesFromEloquentRepository";
    export default {
        data: function () {
            return {
                ajaxReady: true,
                hasFilters: false,
                requestUrl: '/api/file_requests/user',
                container: 'attachable-file-requests',
                urlHistory: false
            }
        },
        computed: {
            attached(){
                return this.file.file_request;
            },
            fileRequests() {
                let data = this.response.data;
                if (!data || data.length === 0) return false;
                return data;
            }
        },
        props: ['project-id', 'file'],
        watch: {
            file(newFile) {
                // Find FileRequest(s) with the same name...
                if (newFile) {
                    this.repoSearch = newFile.name;
                    this.searchTerm();
                }
            }
        },
        methods: {
            attachFileRequest(fileRequestHash){
                if(!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.post(`/api/projects/${ this.projectId }/files/${this.file.id}/attach_fr`, {
                    'file_request_hash': fileRequestHash
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.$emit('attached-request', response.json());
                    this.ajaxReady = true;
                },(response) => {
                    // error
                    console.log("error posting to: `/projects/${ this.file.project_id }/attach`");
                    this.ajaxReady = true;
                });
            }
        },
        mixins: [FetchesFromEloquentRepository],
        created() {
            $(document).on('click.attach-fr', (e) => {
                let container = $("#attach-fr-menu");
            if (!container.is(e.target) // if the target of the click isn't the container...
                && container.has(e.target).length === 0) // ... nor a descendant of the container
            {
                this.$emit('toggle-attach-fr-menu');
            }
            });
        },
        mounted(){
            this.repoSearch = this.file.name;
            this.searchTerm();
        },
        beforeDestroy() {
            $(document).off('click.attach-fr');
            RequestsMonitor.abortRequest(this.request);
        }
    }
</script>