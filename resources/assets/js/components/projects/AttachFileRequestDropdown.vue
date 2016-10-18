<template>
    <li class="dropdown attach-fr-dropdown"
        :class="{
            'open': show
        }"
        @click.stop=""
    >
        <a class="btn btn-primary btn-sm"
           data-toggle="dropdown"
           aria-haspopup="true"
           aria-expanded="false"
           @click.prevent.stop="toggleDropdown"
           :disabled="attached"
        >
            <i class="fa fa-link"></i>Attach
        </a>
        <div class="dropdown-menu dropdown-menu-right">
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
    </li>
</template>
<script>
    import FetchesFromEloquentRepository from "../../mixins/fetchesFromEloquentRepository";
    export default {
        data: function () {
            return {
                ajaxReady: true,
                hasFilters: false,
                requestUrl: '/api/file_requests/user',
                show: false,
                container: 'attachable-file-requests',
                urlHistory: false,
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
                    this.show = false;
                    this.repoSearch = newFile.name;
                    this.searchTerm();
                }
            }
        },
        methods: {
            toggleDropdown(){
                if (this.attached) return;
                this.show = !this.show;
            },
            attachFileRequest(fileRequestHash){
                if(!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.post(`/api/projects/${ this.projectId }/attach_fr`, {
                    'project_file_id': this.file.id,
                    'file_request_hash': fileRequestHash
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.ajaxReady = true;
                },(response) => {
                    // error
                    console.log("error posting to: `/projects/${ this.file.project_id }/attach`");
                    this.ajaxReady = true;
                });
            }
        },
        mounted(){

        },
        mixins: [FetchesFromEloquentRepository]
    }
</script>