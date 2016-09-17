<template>
    <div id="checklist-single">

        <div id="header" class="container-fluid">
            <h4 class="text-center">
                <strong>
                    <span class="text-capitalize">{{ checklist.name }}</span>
                </strong>
            </h4>
        </div>

        <div id="checklist-body">

            <div id="checklist-recipients">
                <div class="recipients" :class="{ expanded: expandRecipients }">
                    <span class="span-to">To: </span>
                    <ul class="recipients-list list-unstyled list-inline">
                        <li v-for="recipient in checklist.recipients">{{ recipient.email }}</li>
                    </ul>
                </div>
                <div class="recipients recipients-sizer" :class="{ expanded: expandRecipients }">
                    <span class="span-to">To: </span>
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
                    <div class="text-right">
                        <a v-show="singleView"
                           @click.prevent="toggleRightPanel"
                           class="btn btn-link pane-nav">
                            <span v-if="selectedFileRequest">File</span><span v-else>Summary</span> <i
                                class="fa fa-angle-double-right"></i>
                        </a>
                    </div>
                    <form id="form-checklist-search" @submit.prevent="searchTerm">
                        <div class="input-group">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">Filters <span class="caret"></span>
                                </button>

                                <file-filters :filter-options="filterOptions" :min-filter-value.sync="minFilterValue"
                                              :max-filter-value.sync="maxFilterValue" :filter.sync="filter"
                                              :filter-value.sync="filterValue" :add-filter="addFilter"></file-filters>

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
                            <li class="menu-item hidden-xs"><a :href="'/fr/' + selectedFileRequest.hash + '/history'"
                                                               :class="{'disabled': ! selectedFileRequest.latest_upload }"><i
                                    class="icon history fa fa-clock-o"></i>History</a></li>
                            <li class="menu-item hidden-xs"><a href="#" @click.prevent="showDeleteModal"><i
                                    class="icon delete fa fa-trash-o"></i>Delete</a></li>
                        </ul>
                    </div>


                    <ul id="files-header"
                        class="list-unstyled list-inline table-header"
                        v-show="! selectedFileRequest"
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
                        <li class="column col-upload header-column">
                            <!-- empty spacer column-->
                        </li>
                    </ul>

                    <ul id="files-list" class="list-unstyled keep-selected-file">
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
                            <div class="column col-upload content-column">
                                <file-uploader :file-request.sync="fileRequest"></file-uploader>
                            </div>
                        </li>
                    </ul>
                </div>
                <div id="right-pane"
                     :class="{
                        'is-main': singleView && showRightPanel,
                        'collapsed': singleView && !showRightPanel
                     }"
                     class="pane"
                >
                    <a v-show="singleView"
                       @click.prevent="toggleRightPanel"
                       class="btn btn-link pane-nav">
                        <i class="fa fa-angle-double-left"></i> List
                    </a>
                    <div id="file-view" class="content" v-if="selectedFileRequest">
                        <a id="back-to-summary" class="btn btn-link" @click.prevent="unselectFileRequest"><i class="fa fa-chevron-left"></i> List
                            Overview</a>
                        <div id="selected-file-requirements">
                            <selected-file-date
                                    :file-request.sync="fileRequests[selectedFileRequestIndex]"></selected-file-date>
                            <selected-file-weighting
                                    :file-request.sync="fileRequests[selectedFileRequestIndex]"></selected-file-weighting>
                        </div>
                        <h3><strong>{{ selectedFileRequest.name }}</strong></h3>
                        <div id="progress-status"
                             :class="{
                                rejected: selectedFileRequest.status === 'rejected',
                                 received: selectedFileRequest.status === 'received'
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
                        <h4><strong>List Overview</strong></h4>
                        <div id="description">
                            <h5><strong>Description</strong></h5>
                            <p v-if="checklist.description">{{ checklist.description }}</p>
                        </div>
                        <div id="files-count">
                            <h5><strong>Files Recevied</strong></h5>
                            <p>{{ checklist.received }} / {{ checklist.requested_files.length }}</p>
                        </div>
                        <div id="list-weighting" v-if="checklist.weightings.set">
                            <h5><strong>Progress</strong></h5>
                            <p>
                                Completed {{ checklist.weightings.progress }}% out of a total of {{
                                checklist.weightings.total }}%
                            </p>
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
                fileRequests: [],
                numReceived: '',
                selectedFileRequestIndex: '',
                singleView: false,
                showRightPanel: false,
                expandRecipients: false
            }
        },
        computed: {
            selectedFileRequest: function () {
                return this.fileRequests[this.selectedFileRequestIndex];
            },
            checklistBelongsToUser: function () {
                if (!this.user) return false;
                return this.user.id === this.checklist.user_id;
            },
            requestUrl: function () {
                return '/c/' + this.checklistHash + '/files';
            },
            canRejectFile: function () {
                if (!this.selectedFileRequest) return false;
                return this.selectedFileRequest.status === 'received';
            },
            receivedFilesPercentage: function () {
                if (!this.response.total) return 0;
                return (100 * this.numReceived / this.response.total).toFixed(2);
            }
        },
        props: ['aws-url', 'user', 'checklist', 'checklist-hash'],
        mixins: [fetchesFromEloquentRepository],
        methods: {
            getFileRequestIndex: function (file) {
                return _.indexOf(this.fileRequests, _.find(this.fileRequests, {id: file.id}));
            },
            unselectFileRequest: function () {
                this.selectedFileRequestIndex = '';
            },
            selectFileRequest: function (index) {
                if (!this.fileRequests[index]) return;  // fr doesn't exist
                this.selectedFileRequestIndex = index;
                this.$nextTick(() => {
                    this.focusOnFileRequest(this.selectedFileRequestIndex);
                });
            },
            focusOnFileRequest: function (index) {
                $('.single-file-request')[index].focus();
            },
            showRejectModal: function () {
                if (this.canRejectFile) vueGlobalEventBus.$emit('show-reject-modal');
            },
            showDeleteModal: function () {
                vueGlobalEventBus.$emit('show-delete-modal', this.selectedFileRequest);
            },
            uploadSelected: function () {
                vueGlobalEventBus.$emit('upload-selected-file-' + this.selectedFileRequest.id);
            },
            showSelectMenuDropdown: function () {
                $('#select-menu-more').next('.dropdown-menu').toggle();
            },
            toggleRightPanel: function () {
                this.showRightPanel = !this.showRightPanel;
            },
            setSplitView: function (element) {
                $(element).css('opacity', 1);
                this.singleView = (element.clientWidth <= 767);
            },
            toggleRecipientsCollapse: function () {
                this.expandRecipients = !this.expandRecipients;
            },
            setRecipientsCollapsability: function (element) {
                let containerWidth = $('#checklist-recipients').width();
                let contentWidth = $('.recipients-sizer').outerWidth() + 10;
                if (contentWidth > containerWidth) {
                    $(element).addClass('expandable');
                } else {
                    $(element).removeClass('expandable');
                }
            }
        },
        ready: function () {

            var self = this;

            // Parse out our request params
            self.$watch('response', (response) => {
                self.fileRequests = $.map(_.omit(response.data, 'query_parameters'), (fileRequest, index) => {
                    return fileRequest;
                });
                self.numReceived = self.params.num_received_files;
            });

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

            $(window).on('load', () => {
                let element = document.getElementById('checklist-body');
                self.setSplitView(element);
                let sensor = new ResizeSensor(element, function () {
                    self.setSplitView(element)
                });
            });

            $(window).on('load', () => {
                let element = document.getElementById('checklist-recipients');
                self.setRecipientsCollapsability(element);
                new ResizeSensor(element, function () {
                    self.setRecipientsCollapsability(element);
                });
            });

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
        }
    };
</script>