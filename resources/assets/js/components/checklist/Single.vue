<template>
    <div id="checklist-single">

        <div id="checklist-body">
            <h3 class="text-capitalize">
                <span class="small text-muted">Documents Checklist</span>
                <br>
                {{ checklist.name }}
            </h3>

            <div id="checklist-recipients">
                <div class="recipients" :class="{ expanded: expandRecipients }">
                    <span class="span-to"><i class="fa fa-users"></i></span>
                    <ul class="recipients-list list-unstyled list-inline">
                        <li v-for="recipient in checklist.recipients">{{ recipient.email }}</li>
                    </ul>
                </div>
                <div class="recipients recipients-sizer" :class="{ expanded: expandRecipients }">
                    <span class="span-to"><i class="fa fa-users"></i></span>
                    <ul class="recipients-list list-unstyled list-inline">
                        <li v-for="recipient in checklist.recipients">{{ recipient.email }}</li>
                    </ul>
                </div>
                <div class="expander"><span @click="toggleRecipientsCollapse"><i v-show="! expandRecipients"
                                                                                 class="fa fa-angle-double-down"></i><i
                        v-else class="fa fa-angle-double-up"></i></span></div>
            </div>

            <div id="split-view" class="keep-selected-file">
                <div id="main-pane"
                     :class="{
                        'collapsed': singleView && showRightPanel
                     }"
                     class="pane"
                >

                    <div class="pane-nav" v-show="singleView">
                        <a @click.prevent="showListOverview"
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

                                    <file-filters :filter-options="filterOptions"
                                                  :min-filter-value.sync="minFilterValue"
                                                  :max-filter-value.sync="maxFilterValue" :filter.sync="filter"
                                                  :filter-value.sync="filterValue"
                                                  :add-filter="addFilter"></file-filters>

                                </div>
                                <input class="form-control input-search"
                                       type="text"
                                       placeholder="Search"
                                       @keyup="searchTerm"
                                       v-model="params.search"
                                       :class="{
                                    'active': params.search && params.search.length > 0
                               }"
                                >
                            </div>
                        </form>

                        <file-active-filters :params.sync="params" :remove-filter="removeFilter"></file-active-filters>

                        <div id="selected-file-menu"
                             class="table-header keep-selected-file"
                             v-if="selectedFileRequest"
                             @click.stop=""
                        >
                            <span class="file-name" v-if="selectedFileRequest">{{ selectedFileRequest.name }}</span>
                            <ul class="list-menu-items list-inline list-unstyled">
                                <li class="menu-item visible-xs-inline">
                                    <a href="#"
                                       :class="{'disabled': selectedFileRequest.status === 'received'}"
                                       @click="uploadSelected"

                                    >
                                        <i class="icon upload fa fa-upload"></i>Upload
                                    </a>
                                </li>
                                <li class="menu-item">
                                    <a href="#"
                                       @click.prevent="showRejectModal"
                                       :class="{ 'disabled': ! canRejectFile }">
                                        <i class="icon reject fa fa-close"></i>Reject
                                    </a>
                                </li>
                                <li class="dropdown visible-xs-inline">
                                    <a id="select-menu-more" @click.prevent.stop="showSelectMenuDropdown"
                                       data-toggle="dropdown"
                                       role="button" aria-haspopup="true"
                                       aria-expanded="false">
                                        More
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="select-menu-more">
                                        <li class="menu-item">
                                            <a :href="'/fr/' + selectedFileRequest.hash + '/history'"
                                               :class="{'disabled': ! selectedFileRequest.latest_upload }">
                                                <i class="icon history fa fa-clock-o"></i>History
                                            </a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#"
                                               @click.prevent="showDeleteModal"
                                            >
                                                <i class="icon delete fa fa-trash-o"></i>Delete
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="menu-item hidden-xs"><a
                                        :href="'/fr/' + selectedFileRequest.hash + '/history'"
                                        :class="{'disabled': ! selectedFileRequest.latest_upload }"><i
                                        class="icon history fa fa-clock-o"></i>History</a></li>
                                <li class="menu-item hidden-xs"><a href="#" @click.prevent="showDeleteModal"><i
                                        class="icon delete fa fa-trash-o"></i>Delete</a></li>
                            </ul>
                        </div>


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
                            <li v-if="checklist.weightings.set"
                                class="column col-weighting header-column"
                                :class="{
                            'current_asc': params.sort === 'weighting' && params.order === 'asc',
                            'current_desc': params.sort === 'weighting' && params.order === 'desc',
                            }"
                                @click="changeSort('weighting')"
                            >
                                %
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

                        <ul id="files-list" class="list-unstyled keep-selected-file" @scroll="scrollList">
                            <li class="single-file-request"
                                v-for="(index, fileRequest) in fileRequests"
                                @focus="selectFileRequest(index)"
                                tabindex="1"
                                :class="{ 'is-selected': fileRequest === selectedFileRequest }"
                                @keydown.up="selectFileRequest(index - 1)"
                                @keydown.down="selectFileRequest(index + 1)"
                            >
                                <div class="column col-file content-column file-status" :class="fileRequest.status">
                                    <i class="fa fa-file-o"></i>
                                </div>
                                <div class="column col-weighting content-column" v-if="checklist.weightings.set">
                                    <span v-if="fileRequest.weighting">{{ fileRequest.weighting }}</span>
                                    <span v-else>--</span>
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
                                    <span v-else class="name">{{ fileRequest.name }}</span>
                                </div>
                                <div class="column col-due content-column">
                                <span class="date no-wrap"
                                      v-if="fileRequest.due">{{ fileRequest.due | smartDate }}</span>
                                    <span v-else>--</span>
                                </div>
                                <div class="column col-file-view content-column">
                                    <button type="button" class="btn btn-primary" @click="showFileView(index)">
                                        <i class="fa fa-arrow-right"></i>
                                    </button>
                                </div>
                                <div class="column col-upload content-column">
                                    <file-uploader :file-request.sync="fileRequest"></file-uploader>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="right-pane"
                     :class="{
                        'is-main': singleView && showRightPanel,
                        'collapsed': singleView && !showRightPanel
                     }"
                     class="pane"
                >

                    <div class="pane-nav" v-show="singleView || selectedFileRequest">
                        <a v-if="singleView"
                           @click.prevent="toggleRightPanel"
                           class="btn btn-link">
                            All Files
                        </a>
                        <a class="btn btn-link" v-if="selectedFileRequest" @click.prevent="unselectFileRequest">List
                            Overview</a>
                    </div>

                    <div class="pane-container">
                        <div id="file-view" class="content" v-if="selectedFileRequest">

                            <div id="selected-file-requirements">
                                <selected-file-date
                                        :file-request.sync="fileRequests[selectedFileRequestIndex]"></selected-file-date>
                                <selected-file-weighting
                                        :file-request.sync="fileRequests[selectedFileRequestIndex]"></selected-file-weighting>
                            </div>
                            <h4>{{ selectedFileRequest.name }}</h4>
                            <div id="progress-status"
                                 :class="{
                                 received: ! selectedFileRequest.uploading && selectedFileRequest.status === 'received',
                                 rejected: ! selectedFileRequest.uploading && selectedFileRequest.status === 'rejected',
                                 }"
                            >
                                <div class="progress-bar"
                                     :style="{
                                        width: selectedFileRequest.uploadProgress + '%'
                                     }"
                                ></div>
                            </div>
                            <ul id="single-file-request-menu" class="list-inline list-unstyled">
                                <li class="menu-item">
                                    <a href="#"
                                       @click.prevent="showRejectModal"
                                       :class="{ 'disabled': ! canRejectFile }">
                                        <i class="icon reject fa fa-close"></i>Reject
                                    </a>
                                </li>
                                <li class="menu-item">
                                    <a :href="'/fr/' + selectedFileRequest.hash + '/history'"
                                       :class="{'disabled': ! selectedFileRequest.latest_upload }">
                                        <i class="icon history fa fa-clock-o"></i>History
                                    </a>
                                </li>
                                <li class="menu-item">
                                    <a href="#"
                                       @click.prevent="showDeleteModal"
                                    >
                                        <i class="icon delete fa fa-trash-o"></i>Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div id="summary-view" class="content" v-else>
                            <h4 class="title-list-overview">List Overview</h4>
                            <div id="description">
                                <h5>Description</h5>
                                <p v-if="checklist.description">{{ checklist.description }}</p>
                            </div>
                            <div id="files-count">
                                <h5>Files Recevied</h5>
                                <p>{{ checklist.received }} / {{ checklist.requested_files.length }}</p>
                            </div>
                            <div id="list-weighting" v-if="checklist.weightings.set">
                                <h5>Progress</h5>
                                <p>
                                    Completed {{ checklist.weightings.progress }}% out of a total of {{
                                    checklist.weightings.total }}%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <file-reject-modal :file="selectedFileRequest"></file-reject-modal>
        <file-delete-modal :file="selectedFileRequest"></file-delete-modal>
    </div>
</template>
<script>

    const fetchesFromEloquentRepository = require('../../mixins/fetchesFromEloquentRepository');

    export default {
        data: function () {
            return {
                ajaxReady: true,
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
                singleView: false,
                showRightPanel: false,
                expandRecipients: false
            }
        },
        computed: {
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
                if (!this.user) return false;
                return this.user.id === this.checklist.user_id;
            },
            requestUrl() {
                return '/c/' + this.checklistHash + '/files';
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
        props: ['aws-url', 'user', 'checklist', 'checklist-hash'],
        mixins: [fetchesFromEloquentRepository],
        methods: {
            getFileRequestIndex(file) {
                return _.indexOf(this.fileRequests, _.find(this.fileRequests, {id: file.id}));
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
            showSelectMenuDropdown() {
                $('#select-menu-more').next('.dropdown-menu').toggle();
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
            setSplitView(element) {
                $(element).css('opacity', 1);
                this.singleView = (element.clientWidth <= 767);
            },
            toggleRecipientsCollapse() {
                this.expandRecipients = !this.expandRecipients;
            },
            setRecipientsCollapsability(element) {
                let containerWidth = $('#checklist-recipients').width();
                let contentWidth = $('.recipients-sizer').outerWidth() + 10;
                if (contentWidth > containerWidth) {
                    $(element).addClass('expandable');
                } else {
                    $(element).removeClass('expandable');
                }
            },
            scrollList: _.throttle(function (event) {
                let el = document.getElementById('files-list');
                if ($(el).innerHeight() + $(el).scrollTop() >= (el.scrollHeight - 100)) this.fetchNextPage();
            }, 100)
        },
        ready: function () {

            var self = this;

//            ====================================================================================================================================
//            NOT IMPLEMENTED
//            Can't make it play nicely with datepicker. Datepicker prev/next month clicks aren't registering.
//            ====================================================================================================================================
//            // Click event bind - Unselect file if we didn't click inside list or within select menu
//            // Elements that even if we click on, we don't want to lose selectedFileRequest
//            $(document).on('click', (e) => {
//                let focusContainers = $('.keep-selected-file');
//                let clickedInside = false;
//                for (var i = 0; i < focusContainers.length; i++) {
//                    if ($(focusContainers[i]).is(e.target) || $(focusContainers[i]).has(e.target).length !== 0) {
//                        clickedInside = true;
//                        break;
//                    }
//                }
//                if (!clickedInside) self.selectedFileRequestIndex = '';
//            });

            // When updated file request model
            vueGlobalEventBus.$on('updated-file-request', (updatedFileRequest) => {
                let index = this.getFileRequestIndex(updatedFileRequest);
                this.$set('fileRequests[' + index + ']', updatedFileRequest);
            });

            // When we delete a file
            vueGlobalEventBus.$on('deleted-selected-file', () => {
                this.fileRequests.splice(this.selectedFileRequestIndex, 1);
                this.selectedFileRequestIndex = '';
            });

            // Sensor for split view
            $(window).on('load', () => {
                let element = document.getElementById('checklist-body');
                self.setSplitView(element);
                let sensor = new ResizeSensor(element, function () {
                    self.setSplitView(element)
                });
            });

            // Sensor for recipients
            $(window).on('load', () => {
                let element = document.getElementById('checklist-recipients');
                self.setRecipientsCollapsability(element);
                new ResizeSensor(element, function () {
                    self.setRecipientsCollapsability(element);
                });
            });

            // Update checklist weightings
            vueGlobalEventBus.$on('updated-weighting', () => {
                this.$http.get('/c/' + this.checklist.hash + '/weightings')
                        .then((response) => {
                            // Success
                            this.checklist.weightings = JSON.parse(response.data);
                        }, (response) => {
                            // error
                            console.log('GET REQ Error!');
                            console.log(response);
                        })
            });

            // Check if we need to fetch more data for inf. load
            $(window).on('load', () => {
                this.scrollList();
            });


        }
    };
</script>