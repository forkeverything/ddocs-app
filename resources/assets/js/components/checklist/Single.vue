<template>
    <div id="checklist-single">
        <rectangle-loader :loading="initializing" size="large"></rectangle-loader>
        <div id="checklist-body">
            <h3 class="text-capitalize">
                <span class="small text-muted">Checklist</span>
                <br>
                {{ checklist.name }}
            </h3>

            <checklist-recipients :recipients="checklist.recipients"></checklist-recipients>

            <div id="split-view">
                <div id="main-pane"
                     :class="{
                        'collapsed':  showRightPanel
                     }"
                     class="pane"
                >

                    <div class="pane-nav">
                        <a
                                @click.prevent="showListOverview"
                                class="btn btn-link"
                        >
                            <span>List Overview</span>
                        </a>
                    </div>

                    <div class="pane-container">

                        <form id="form-checklist-search" @submit.prevent="searchTerm" v-if="params">
                            <div class="input-group">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">Filters <span
                                            class="caret"></span>
                                    </button>

                                    <fr-filters :filter-options="filterOptions"
                                                  @add-filter="addFilter">
                                    </fr-filters>

                                </div>
                                <input class="form-control input-search"
                                       type="text"
                                       placeholder="Search"
                                       @keyup="searchTerm"
                                       v-model="repoSearch"
                                       :class="{
                                            'active': repoSearch && repoSearch.length > 0
                                       }"
                                >
                            </div>
                        </form>

                        <fr-active-filters :params="params" :remove-filter="removeFilter"></fr-active-filters>

                        <mobile-file-menu v-if="selectedFileRequest" :selected-file-request="selectedFileRequest"
                                          :show-delete-modal="showDeleteModal" :upload-selected="uploadSelected"
                                          :show-reject-modal="showRejectModal"
                                          :can-reject-file="canRejectFile"></mobile-file-menu>


                        <ul id="files-header"
                            class="list-unstyled list-inline table-header"
                            v-if="params"
                        >
                            <li class="column col-file header-column"
                                :class="{
                            'current_asc': params.sort === 'status' && params.order === 'asc',
                            'current_desc': params.sort === 'status' && params.order === 'desc',
                            }"
                                @click="changeSort('status')"
                            >
                                <i class="fa fa-file-o"></i>
                            </li>
                            <li class="column col-name header-column"
                                :class="{
                            'current_asc': params.sort === 'name' && params.order === 'asc',
                            'current_desc': params.sort === 'name' && params.order === 'desc',
                            }"
                                @click="changeSort('name')"
                            >
                                File
                            </li>
                            <li class="column col-due header-column"
                                :class="{
                            'current_asc': params.sort === 'due' && params.order === 'asc',
                            'current_desc': params.sort === 'due' && params.order === 'desc',
                            }"
                                @click="changeSort('due')"
                            >
                                Due
                            </li>
                            <li class="column col-file-view header-column">
                                <!-- empty spacer column-->
                            </li>
                            <li class="column col-upload header-column">
                                <!-- empty spacer column-->
                            </li>
                        </ul>

                        <ul id="files-list" class="list-unstyled" @scroll="scrollList">
                            <li class="single-file-request"
                                v-for="(fileRequest, index) in fileRequests"
                                @focus="selectFileRequest(index)"
                                tabindex="1"
                                :class="{ 'is-selected': fileRequest === selectedFileRequest }"
                                @keydown.up="selectFileRequest(index - 1)"
                                @keydown.down="selectFileRequest(index + 1)"
                            >
                                <div class="column col-file content-column file-status" :class="fileRequest.status">
                                    <i class="fa fa-file-o"></i>
                                </div>
                                <div class="column col-name content-column">
                                    <!-- Download -->
                                    <a v-if="fileRequest.latest_upload"
                                       :href=" awsUrl + fileRequest.latest_upload.path"
                                       :alt="fileRequest.name + 'download link'"
                                       class="name"
                                    >
                                        {{ fileRequest.name }}
                                    </a>
                                    <span v-if="! fileRequest.latest_upload" class="name">{{ fileRequest.name }}</span>
                                </div>
                                <div class="column col-due content-column">
                                    <smart-date v-if="fileRequest.due" :date="fileRequest.due"></smart-date>
                                    <span v-if="! fileRequest.due">--</span>
                                </div>
                                <div class="column col-file-view content-column">
                                    <button type="button" class="btn btn-primary" @click="showFileView(index)">
                                        <i class="fa fa-arrow-right"></i>
                                    </button>
                                </div>
                                <div class="column col-upload content-column">
                                    <fr-uploader :index="index" :file-request="fileRequest"
                                                   @update-file-request="updateFileRequest"></fr-uploader>
                                </div>
                            </li>
                            <li class="fr-loading" v-if="hasNextPage">
                                <rectangle-loader :loading="true"></rectangle-loader>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="right-pane"
                     :class="{
                        'collapsed':  ! showRightPanel
                     }"
                     class="pane"
                >

                    <div class="pane-nav"
                         :class="{
                            'selected-fr': selectedFileRequest
                            }"
                    >
                        <a @click.prevent="toggleRightPanel"
                           class="btn btn-link link-files-list"
                        >
                            All Files
                        </a>
                        <a class="btn btn-link" v-if="selectedFileRequest" @click.prevent="unselectFileRequest">List
                            Overview</a>
                    </div>

                    <div class="pane-container">
                        <file-view :is-owner="checklistBelongsToUser" v-if="selectedFileRequest"
                                   :selected-file-request-index="selectedFileRequestIndex"
                                   :selected-file-request="selectedFileRequest" :show-reject-modal="showRejectModal"
                                   :can-reject-file="canRejectFile" :show-delete-modal="showDeleteModal"></file-view>
                        <summary-view v-if="! selectedFileRequest" :checklist="checklist"></summary-view>
                    </div>
                </div>
            </div>
        </div>

        <fr-reject-modal :index="selectedFileRequestIndex" :selected-file-request="selectedFileRequest"
                           @update-file-request="updateFileRequest"></fr-reject-modal>
        <fr-delete-modal :selected-file-request="selectedFileRequest" :index="selectedFileRequestIndex"
                           @remove-file-request="removeFileRequest"></fr-delete-modal>
    </div>
</template>
<script>

    const fetchesFromEloquentRepository = require('../../mixins/fetchesFromEloquentRepository');

    export default {
        data: function () {
            return {
                ajaxReady: true,
                awsUrl: awsURL,
                checklist: '',
                hasFilters: true,
                container: 'files-list',
                filterOptions: [
                    {
                        value: 'version',
                        label: 'Version'
                    },
                    {
                        value: 'due',
                        label: 'Due Date'
                    },
                    {
                        value: 'status',
                        label: 'Status'
                    }
                ],
                selectedFileRequestIndex: '',
                showRightPanel: false
            }
        },
        computed: {
            initializing() {
                return ! this.response;
            },
            authenticatedUser(){
                return this.$store.state.authenticatedUser;
            },
            fileRequests() {
                return this.response.data;
            },
            numReceived(){
                return this.response.query_parameters.num_received_files;
            },
            selectedFileRequest() {
                if (!this.fileRequests) return;
                return this.fileRequests[this.selectedFileRequestIndex];
            },
            checklistBelongsToUser() {
                if (!this.authenticatedUser) return false;
                return this.authenticatedUser.id === this.checklist.user_id;
            },
            requestUrl() {
                return '/api/c/' + this.$route.params.checklist_hash + '/files';
            },
            canRejectFile() {
                if (!this.selectedFileRequest) return false;
                return this.selectedFileRequest.status === 'received';
            },
            receivedFilesPercentage() {
                if (!this.response.total) return 0;
                return (100 * this.numReceived / this.response.total).toFixed(2);
            }
        },
        watch: {
          response() {
              this.$nextTick(this.setFilesHeaderScrollbarPadding);
          }
        },
        mixins: [fetchesFromEloquentRepository],
        methods: {
            updateFileRequest(newFileRequestObject, index) {
                Vue.set(this.fileRequests, index, newFileRequestObject);
            },
            removeFileRequest(index){
                this.fileRequests.splice(index, 1);
                this.selectedFileRequestIndex = '';
            },
            unselectFileRequest() {
                this.selectedFileRequestIndex = '';
            },
            selectFileRequest(index) {
                if (!this.fileRequests[index]) return;  // fr doesn't exist
                this.selectedFileRequestIndex = index;
                this.$nextTick(() => {
                    this.focusOnFileRequest(this.selectedFileRequestIndex);
                });
            },
            focusOnFileRequest(index) {
                $('.single-file-request')[index].focus();
            },
            showRejectModal() {
                if (this.canRejectFile) vueGlobalEventBus.$emit('show-reject-modal');
            },
            showDeleteModal() {
                vueGlobalEventBus.$emit('show-delete-modal', this.selectedFileRequest);
            },
            uploadSelected() {
                vueGlobalEventBus.$emit('upload-selected-file-' + this.selectedFileRequest.id);
            },
            toggleRightPanel() {
                this.showRightPanel = !this.showRightPanel;
            },
            showFileView(index) {
                this.selectFileRequest(index);
                this.toggleRightPanel();
            },
            showListOverview()  {
                this.unselectFileRequest();
                this.toggleRightPanel();
            },
            addChecklistNameToUrl(){
                let checklistName = this.checklist.name.replace(/\s+/g, '-').toLowerCase();
                // build query string from router prop
                let queryString = '';
                if (Object.keys(this.$route.query).length > 0) {
                    queryString += '?';
                    for (let prop in this.$route.query) {
                        queryString += prop + '=' + this.$route.query[prop] + '&';
                    }
                    queryString = queryString.substr(0, queryString.length - 1);
                }
                // use history api instead of router.replace so we don't trigger the beforeEach hook
                window.history.replaceState({}, '', `/c/${ this.$route.params.checklist_hash }/${ checklistName }${ queryString }`);
            },
            fetchChecklist(){
                this.$http.get(`/api/c/${ this.$route.params.checklist_hash }`, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((res) => {
                    this.checklist = res.json();
                    this.addChecklistNameToUrl();
                }, (res) => {
                    console.log("error fetching checklist");
                });
            },
            setFilesHeaderScrollbarPadding() {
                let header = document.getElementById('files-header');
                let list = document.getElementById('files-list');
                if(header && list) header.style.paddingRight = list.offsetWidth - list.clientWidth + 'px';
            }
        },
        created() {
            window.addEventListener('resize', this.setFilesHeaderScrollbarPadding)
        },
        mounted() {
            this.fetchChecklist();
            this.$nextTick(this.setFilesHeaderScrollbarPadding);
        },
        beforeDestroy() {
            window.removeEventListener('resize', this.setFilesHeaderScrollbarPadding)
        }
    };
</script>