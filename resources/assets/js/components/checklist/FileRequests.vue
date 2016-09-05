<template>
    <div id="checklist-file-requests">
        <div id="fr-body">
            <div id="fr-center-pane">
                <form id="form-checklist-search" @submit.prevent="searchTerm">
                    <div class="input-group">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">Filters <span class="caret"></span></button>
                            <ul class="dropdown-menu filters-menu" @click.stop="">
                                <li>
                                    <p class="text-muted">Show where</p>
                                    <select class="form-control select-filter" v-model="filter" placeholder="Select one...">
                                        <option value="" selected disabled>Select filter</option>
                                        <option v-for="option in filterOptions" :value="option.value">{{ option.label }}
                                        </option>
                                    </select>


                                    <!-- Filter: Version -->
                                    <p class="text-muted" v-show="filter === 'version'">is between</p>
                                    <div class="filter-fields version" v-show="filter === 'version'">
                                        <integer-range-field :min.sync="minFilterValue"
                                                             :max.sync="maxFilterValue"></integer-range-field>
                                    </div>

                                    <!-- Filter: Due (Date) -->
                                    <p class="text-muted" v-show="filter === 'due'">is between</p>
                                    <div class="filter-fields due" v-show="filter === 'due'">
                                        <date-range-field :min.sync="minFilterValue"
                                                          :max.sync="maxFilterValue"></date-range-field>
                                    </div>

                                    <!-- Filter: Status -->
                                    <div class="filter-fields status" v-show="filter === 'status'">
                                        <p class="text-muted">is</p>
                                        <select v-model="filterValue" class="form-control">
                                            <option value="" selected disabled>Pick one</option>
                                            <option value="received">Received</option>
                                            <option value="waiting">Waiting</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </div>

                                    <!-- Filter Button -->
                                    <button class="button-add-filter btn btn-default"
                                            v-show="filter && (filterValue || minFilterValue || maxFiltervalue)"
                                            @click.stop.prevent="addFilter">Add Filter
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <input class="form-control input-search"
                               type="text"
                               placeholder="Search..."
                               @keyup="searchTerm"
                               v-model="params.search"
                               :class="{
                                    'active': params.search && params.search.length > 0
                               }"
                        >
                    </div>
                </form>

                <div class="active-filters">

                    <!-- Active Filter: Version -->
                    <button type="button" v-if="params.version_filter_integer" class="btn button-remove-filter" @click="
                        removeFilter('version')">
                        <span class="field">File Version: </span><span
                            v-if="params.version_filter_integer[0]">{{ params.version_filter_integer[0] }}</span><span
                            v-else>~ </span><span
                            v-if="params.version_filter_integer[0] && params.version_filter_integer[1]"> - </span><span
                            v-if="params.version_filter_integer[1]">{{ params.version_filter_integer[1] }}</span><span
                            v-else> ~</span></button>

                    <!-- Active Filter: Due (date) -->
                    <button type="button" v-if="params.due_filter_date" class="btn button-remove-filter" @click="
                        removeFilter('due')"><span
                            class="field">Due: </span><span v-if="params.due_filter_date[0]">{{ params.due_filter_date[0] | date }}</span>
                        <span v-else>~ </span><span
                                v-if="params.due_filter_date[0] && params.due_filter_date[1]"> - </span><span
                                v-if="params.due_filter_date[1]">{{ params.due_filter_date[1] | date }}</span><span
                                v-else> ~</span></button>

                    <!-- Active Filter: Status -->
                    <button type="button" v-if="params.status" class="btn button-remove-filter" @click="removeFilter('status')">
                        Status: {{ params.status }}
                    </button>
                </div>

                <p class="text-muted small text-right">* maximum file size 1GB</p>
                <div id="files-collection">
                    <ul id="files-header" class="list-unstyled list-inline">
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
                        <li class="column col-upload header-column">

                        </li>
                    </ul>
                    <ul id="files-list" class="list-unstyled">
                        <li class="single-file" v-for="file in files" @click="toggleSelectFile(file)"
                            :class="{
                        'is-selected': selectedFile === file
                    }"
                        >
                            <div class="column col-file content-column file-status" :class="file.status">
                                <i class="fa fa-file-o"></i>
                            </div>
                            <div class="column col-name content-column">
                        <span class="name">
                            {{ file.name }}
                        </span>
                        <span class="file-status" :class="file.status">
                            <i class="fa fa-file-o"></i>
                        </span>
                            </div>
                            <div class="column col-due content-column">
                                <span class="date" v-if="file.due">{{ file.due | date }}</span>
                                <span v-else>--</span>
                            </div>
                            <div class="column col-upload content-column">
                                <!-- Upload -->
                                <button :id="'upload-button-file-' + file.id"
                                        type="button" class="btn btn-unstyled button-upload"
                                        :data-file="file.id "
                                        :disabled="file.status === 'received'"
                                >
                                    <i class="fa fa-upload"></i>
                                </button>
                                <input :id="'input-file-' + file.id"
                                       type="file"
                                       name="file"
                                       class="input-file-upload hide"
                                       @change="uploadFile(file, $event)"
                                >
                                <span :id="'upload-progress-file-' + file.id" class="upload-percentage">{{ file.upload_percentage }}</span>
                            </div>
                            <div class="expanded-view" v-show="selectedFile === file">
                                <div class="table-versions-wrap">
                                    <!-- Versions Table -->
                                    <table class="table table-versions">
                                        <thead>
                                        <tr>
                                            <th>Version</th>
                                            <th>Details</th>
                                            <th></th>
                                        <tr>
                                        </thead>
                                        <tbody>
                                        <template v-if="file.uploads.length > 0">
                                            <tr v-for="(index, upload) in file.uploads" >
                                                <td class="fit-to-content text-center">{{ index + 1 }}</td>
                                                <td>
                                            <span v-if="upload.rejected">
                                                {{ upload.rejected_reason }}
                                            </span>
                                                    <span v-else>-</span>
                                                </td>
                                                <td class="fit-to-content no-wrap col-version-controls">
                                                    <!-- Reject -->
                                                    <button v-if="! canUpload"
                                                            type="button"
                                                            class="btn btn-unstyled button-reject file-buttons"
                                                            @click.stop="toggleRejectPanel(file)"
                                                            :disabled="file.status !== 'received' || index + 1 !== file.uploads.length"
                                                    >
                                                        <i class="fa fa-close"></i> Reject
                                                    </button>
                                                    <!-- Download -->
                                                    <a :href=" awsUrl + upload.path" :alt="file.name + 'download link'">
                                                        <button type="button" class="btn btn-unstyled button-download file-buttons"><i
                                                                class="fa fa-download "></i>
                                                            Download
                                                        </button>
                                                    </a>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr v-else>
                                            <td class="fit-to-content text-center">1</td>
                                            <td><span class="text-muted">waiting upload</span></td>
                                            <td class="fit-to-content no-wrap">
                                                <button type="button" class="btn file-buttons btn-unstyled button-reject" disabled><i class="fa fa-close"></i>
                                                    Reject
                                                </button>
                                                <button type="button" class="btn file-buttons btn-unstyled button-download" disabled>
                                                    <i class="fa fa-download"></i>
                                                    Download
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <form class="reject-panel panel panel-default panel-floating"
                                          v-if="! canUpload || index + 1 === file.uploads.length"
                                          @submit.prevent="rejectFile(file)"
                                          v-show="fileToReject === file"
                                    >
                                        <div class="panel-heading">
                                            Reason / Changes Required
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <textarea rows="3" class="form-control autosize" v-model="reason"></textarea>
                                            </div>
                                            <div class="text-right">
                                                <button type="button" class="btn btn-outline-grey btn-space btn-sm"
                                                        @click="toggleRejectPanel"
                                                >Cancel
                                                </button>
                                                <button type="submit" class="btn btn-solid-red btn-sm">Reject</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="page-controls">
                    <per-page-picker :response="response" :req-function="fetchResults"></per-page-picker>
                    <paginator :response="response" :req-function="fetchResults"></paginator>
                </div>
            </div>
            <div id="fr-right-pane" :class="{ 'expanded': selectedFile }">
                <div class="header">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true" @click="toggleSelectFile">&times;</span></button>
                    <h4 class="file-name">{{ selectedFile.name }}</h4>
                    <span class="date-due">{{ selectedFile.due | date }}</span>
                </div>
                <div class="body">
                    <!-- Versions Table -->
                    <table class="table table-versions">
                        <thead>
                        <tr>
                            <th>Version</th>
                            <th>Details</th>
                            <th></th>
                        <tr>
                        </thead>
                        <tbody>
                        <template v-if="selectedFile && selectedFile.uploads.length > 0">
                            <tr v-for="(index, upload) in selectedFile.uploads" >
                                <td class="fit-to-content text-center">{{ index + 1 }}</td>
                                <td>
                                            <span v-if="upload.rejected">
                                                {{ upload.rejected_reason }}
                                            </span>
                                    <span v-else>-</span>
                                </td>
                                <td class="fit-to-content no-wrap col-version-controls">
                                    <!-- Reject -->
                                    <button v-if="! canUpload"
                                            type="button"
                                            class="btn btn-unstyled button-reject file-buttons"
                                            @click="toggleRejectPanel(selectedFile)"
                                            :disabled="selectedFile.status !== 'received' || index + 1 !== selectedFile.uploads.length"
                                    >
                                        <i class="fa fa-close"></i>
                                    </button>
                                    <!-- Download -->
                                    <a :href=" awsUrl + upload.path" :alt="selectedFile.name + 'download link'">
                                        <button type="button" class="btn btn-unstyled button-download file-buttons"><i
                                                class="fa fa-download "></i>
                                        </button>
                                    </a>
                                </td>
                            </tr>
                        </template>
                        <tr v-else>
                            <td class="fit-to-content text-center">1</td>
                            <td><span class="text-muted">waiting upload</span></td>
                            <td class="fit-to-content no-wrap">
                                <button type="button" class="btn file-buttons btn-unstyled button-reject" disabled><i class="fa fa-close"></i>
                                </button>
                                <button type="button" class="btn file-buttons btn-unstyled button-download" disabled>
                                    <i class="fa fa-download"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <form class="reject-panel panel panel-default panel-floating"
                          v-if="! canUpload || index + 1 === selectedFile.uploads.length"
                          @submit.prevent="rejectFile(selectedFile)"
                          v-show="fileToReject && fileToReject === selectedFile"
                    >
                        <div class="panel-heading">
                            Reason / Changes Required
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <textarea rows="3" class="form-control autosize" v-model="reason"></textarea>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-outline-grey btn-space btn-sm"
                                        @click="toggleRejectPanel"
                                >Cancel
                                </button>
                                <button type="submit" class="btn btn-solid-red btn-sm">Reject</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    const fetchesFromEloquentRepository = require('../../mixins/fetchesFromEloquentRepository');

    export default {
        name: 'checklistFileRequests',
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
                files: [],
                selectedFile: '',
                fileWithExpandedDetails: '',           // Holds a file object
                expandedView: '',                      // 'history', 'reject'
                reason: '',
                numReceived: '',
                fileToReject: ''
            }
        },
        props: ['checklist-hash', 'can-upload', 'aws-url'],
        computed: {
            requestUrl: function () {
                return '/checklist/' + this.checklistHash + '/files';
            },
            receivedFilesPercentage: function () {
                if (!this.response.total) return 0;
                return (100 * this.numReceived / this.response.total).toFixed(2);
            }
        },
        mixins: [fetchesFromEloquentRepository],
        methods: {
            toggleSelectFile: function(file) {
                this.fileToReject = '';
                if(file.id) {
                    this.selectedFile = file;
                } else {
                    this.selectedFile = '';
                }
            },
            toggleRejectPanel: function(file) {
                if(file) {
                    this.fileToReject = file;
                } else {
                    this.fileToReject = '';
                }
            },
            getUploadDate: function (file) {
                return moment(file.created_at).format('DDMMYYYY');
            },
            uploadFile: function (file, $event) {

                var self = this;
                if (!self.ajaxReady) return;
                self.ajaxReady = false;

                var fd = new FormData();
                var uploadedFile = $event.srcElement.files[0];

                fd.append('file', uploadedFile);

                self.$http.post('/file/' + file.id, fd, {
                            progress: (event) => {
                            var progress = Math.round(100 * event.loaded / event.total);


                $('#upload-button-file-' + file.id).hide();
                $('#upload-progress-file-' + file.id).text(progress + '%');
            }
            }).then((response) => {
                    // success
                    self.replaceFile(JSON.parse(response.data));
                self.numReceived++;
                self.ajaxReady = true;
            }, (response) => {
                    // error
                    $('#upload-button-file-' + file.id).hide();
                    $('#upload-progress-file-' + file.id).text('');
                    console.log('GET REQ Error!');
                    console.log(response);
                    self.ajaxReady = true;
                });
            },
            replaceFile: function (updatedFileModel) {
                var self = this;
                var index = _.indexOf(self.files, _.find(self.files, {id: updatedFileModel.id}));
                self.files.splice(index, 1, updatedFileModel);
            },
            expandFileSection: function (file, section) {
                this.reason = '';
                this.fileWithExpandedDetails = file;
                this.expandedView = section;
            },
            fileExpanded: function (file) {
                return this.fileWithExpandedDetails === file;
            },
            hideDetailsSection: function () {
                this.fileWithExpandedDetails = '';
            },
            rejectFile: function (file) {
                var self = this;
                if (!self.ajaxReady) return;
                self.ajaxReady = false;
                $.ajax({
                    url: '/file/' + file.id + '/reject',
                    method: 'POST',
                    data: {
                        "reason": self.reason
                    },
                    success: function (file) {
                        // success
                        self.reason = '';
                        self.replaceFile(file);
                        self.numReceived--;
                        self.ajaxReady = true;
                    },
                    error: function (response) {
                        console.log(response);
                        self.ajaxReady = true;
                    }
                });
            }
        },
        ready: function () {

            var self = this;

            self.$watch('response', (response) => {
                self.files = $.map(_.omit(response.data, 'query_parameters'), (file, index) => {
                        return file;
        });
            self.numReceived = self.params.num_received_files;
        });

            $(document).on('click', '.button-upload', function (e) {
                e.preventDefault();
                $('#input-file-' + $(this).data('file')).click();
            });

        }
    }
</script>