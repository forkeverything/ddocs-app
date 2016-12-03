<template>
    <div id="checklist-single" class="main-content">
        <rectangle-loader :loading="initializingRepo" size="large"></rectangle-loader>
        <div id="checklist-body">
            <div id="pane-nav">
                <a @click.prevent="toggleRightPanel"
                   :class="{
                        'active': ! showRightPanel
                   }"
                >List</a>
                <a @click.prevent="showSummary"
                   :class="{
                        'active': showingSummary
                   }"
                >Summary</a>
                <a class="unclickable"
                   :class="{
                       'active': selectedFileRequest && showRightPanel
                   }"
                >
                    File Details
                </a>
            </div>

            <div id="split-view">
                <div id="main-pane"
                     :class="{
                        'collapsed':  showRightPanel
                     }"
                     class="pane"
                >
                    <div class="pane-container">

                        <add-file-request v-if="checklistBelongsToUser"
                                          @added="insertFileRequest"
                        ></add-file-request>

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

                        <mobile-file-menu v-if="selectedFileRequest"
                                          :selected-file-request="selectedFileRequest"
                                          :can-reject-file="canRejectFile"
                                          @file-view="toggleRightPanel"
                                          @history="showHistoryModal"
                                          @reject="showRejectModal"
                                          @delete="showDeleteModal"
                        ></mobile-file-menu>


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
                        </ul>

                        <ul id="files-list" class="list-unstyled" @scroll="scrollList">
                            <li class="single-file-request"
                                v-for="(fileRequest, index) in fileRequests"
                                :key="fileRequest.hash"
                                @focus="selectFileRequest(index)"
                                tabindex="1"
                                :class="{ 'is-selected': fileRequest === selectedFileRequest }"
                                @keydown.up="selectFileRequest(index - 1)"
                                @keydown.down="selectFileRequest(index + 1)"
                            >
                                <div class="column col-file content-column file-status" :class="fileRequest.status">
                                    <i class="fa fa-file"></i>
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
                                    <fr-uploader :file-request="fileRequest"
                                                 @update-file-request="updateFileRequest"
                                    ></fr-uploader>
                                    <fr-due-date :checklist-belongs-to-user="checklistBelongsToUser"
                                                 :file-request="fileRequest"
                                                 @update-file-request="updateFileRequest"
                                    ></fr-due-date>
                                </div>
                            </li>
                            <li class="fr-loading" v-if="hasNextPage">
                                <rectangle-loader :loading="true" size="small"></rectangle-loader>
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

                    <div class="pane-container">
                        <file-view v-if="selectedFileRequest"
                                   @close="closeFileView"
                                   @history="showHistoryModal"
                                   @reject="showRejectModal"
                                   @delete="showDeleteModal"
                                   @update-file-request="updateFileRequest"
                                   :selected-file-request="selectedFileRequest"
                                   :can-reject-file="canRejectFile"
                                   :checklist-belongs-to-user="checklistBelongsToUser"
                        ></file-view>
                        <summary-view v-if="! selectedFileRequest" @delete-checklist="deleteChecklist" :checklist-belongs-to-user="checklistBelongsToUser"></summary-view>
                    </div>
                </div>
            </div>
        </div>
        <fr-history-modal :selected-file-request="selectedFileRequest"
        ></fr-history-modal>
        <fr-reject-modal :selected-file-request="selectedFileRequest"
                         @update-file-request="updateFileRequest"
        ></fr-reject-modal>
        <fr-delete-modal :selected-file-request="selectedFileRequest"
                         :index="selectedFileRequestIndex"
                         @remove-file-request="removeFileRequest"
        ></fr-delete-modal>
    </div>
</template>
<script>

    const fetchesFromEloquentRepository = require('../../mixins/fetchesFromEloquentRepository');

    export default {
        data: function () {
            return {
                ajaxReady: true,
                awsUrl: awsURL,
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
                showRightPanel: false,
                password: ''
            }
        },
        computed: {
            requestUrl() {
                return '/api/c/' + this.$route.params.checklist_hash + '/files';
            },
            checklist() {
                return this.$store.state.checklist.data;
            },
            authenticatedUser(){
                return this.$store.state.authenticatedUser;
            },
            fileRequests() {
                // Avoid duplicates that might occur from adding a new File that
                // would be fetched from a later page.
                return _.uniqBy(this.response.data, 'hash');
            },
            numReceived(){
                return this.response.query_parameters.num_received_files;
            },
            selectedFileRequest() {
                if (!this.fileRequests) return;
                return this.fileRequests[this.selectedFileRequestIndex];
            },
            showingSummary() {
                return this.showRightPanel && !this.selectedFileRequest;
            },
            checklistBelongsToUser() {
                if (!this.authenticatedUser) return false;
                return this.authenticatedUser.id === this.checklist.user_id;
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
            '$route.params.checklist_hash'() {
                this.response = '';
                this.fetchChecklist();
                this.repoSearch = getParameterByName('search');
                this.fetchResults();
                if(this.urlHistory) onPopCallFunction(this.fetchResults);
                if(this.infScroll) this.$nextTick(this.scrollList);
                this.$nextTick(this.setFilesHeaderScrollbarPadding);
            },
            response() {
                this.$nextTick(this.setFilesHeaderScrollbarPadding);
            }
        },
        methods: {
            insertFileRequest(fileRequest){
                this.response.data.unshift(fileRequest);
                this.$nextTick(() => this.selectedFileRequestIndex = 0);
            },
            updateFileRequest(newFileRequestObject) {
                // Modify response object directly otherwise updates don't occur immediately.
                let index = _.indexOf(this.response.data, _.find(this.response.data, (fr) => {
                    return fr.hash === newFileRequestObject.hash;
                }));
                Vue.set(this.response.data, index, newFileRequestObject);
            },
            removeFileRequest(index){
                this.fileRequests.splice(index, 1);
                this.selectedFileRequestIndex = '';
            },
            unselectFileRequest() {
                this.selectedFileRequestIndex = '';
            },
            closeFileView(){
                this.unselectFileRequest();
                this.showRightPanel = false;
            },
            selectFileRequest(index) {
                if (!this.fileRequests[index]) return;  // fr doesn't exist
                this.selectedFileRequestIndex = index;
                this.$nextTick(() => $('.single-file-request')[index].focus());
            },
            showHistoryModal() {
                if (this.selectedFileRequest.latest_upload) vueGlobalEventBus.$emit('show-history-modal');
            },
            showRejectModal() {
                if (this.canRejectFile) vueGlobalEventBus.$emit('show-reject-modal');
            },
            showDeleteModal() {
                vueGlobalEventBus.$emit('show-delete-modal', this.selectedFileRequest);
            },
            toggleRightPanel() {
                this.showRightPanel = !this.showRightPanel;
            },
            showSummary()  {
                this.unselectFileRequest();
                this.showRightPanel = true;
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
                this.$http.post(`/api/c/${ this.$route.params.checklist_hash }`, {
                    password: this.password
                },{
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((res) => {
                    this.$store.commit('checklist/SET', res.json());
                    this.$nextTick(() => {
                        this.addChecklistNameToUrl();
                        this.$store.commit('setTitle', `${ this.checklist.name }<i class="fa fa-list"></i>`);
                    });
                }, (res) => {
                    console.log("error fetching checklist");
                    if(res.status === 404) router.push('/error/404/checklist');
                });
            },
            setFilesHeaderScrollbarPadding() {
                let header = document.getElementById('files-header');
                let list = document.getElementById('files-list');
                if (header && list) header.style.paddingRight = list.offsetWidth - list.clientWidth + 'px';
            },
            deleteChecklist() {
                if(!this.ajaxReady) return;
                this.ajaxReady = false;
                this.$http.delete(`/api/c/${ this.checklist.hash }`, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((res) => {
                    router.push('/checklists');
                }, (res) => {
                    console.log('error deleting checklist.');
                    this.ajaxReady = true;
                });
            }
        },
        mixins: [fetchesFromEloquentRepository],
        created() {
            window.addEventListener('resize', this.setFilesHeaderScrollbarPadding)
        },
        mounted() {
            this.fetchChecklist();
            this.$nextTick(this.setFilesHeaderScrollbarPadding);
        },
        beforeDestroy() {
            window.removeEventListener('resize', this.setFilesHeaderScrollbarPadding);
            this.$store.commit('checklist/SET', '');
        }
    };
</script>