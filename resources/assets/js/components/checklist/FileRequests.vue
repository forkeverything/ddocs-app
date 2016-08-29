<template>
    <div id="checklist-file-requests">
        <form id="form-checklist-search" @submit.prevent="searchTerm">
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Filters <span class="caret"></span></button>
                    <ul class="dropdown-menu filters-menu" @click.stop="">
                        <li>
                            <p class="text-muted">Show where</p>
                            <select class="form-control select-filter" v-model="filter" placeholder="Select one...">
                                <option value="" selected disabled>Select filter</option>
                                <option v-for="option in filterOptions" :value="option.value">{{ option.label }}</option>
                            </select>

                            <!-- Filter: Requirement -->
                            <div class="filter-fields requirement" v-show="filter === 'required'">
                                <p class="text-muted">is</p>
                                <select v-model="filterValue" class="form-control">
                                    <option value="" selected disabled>Pick one</option>
                                    <option value="1">Compulsory</option>
                                    <option value="0">Optional</option>
                                </select>
                            </div>

                            <!-- Filter: Version -->
                            <p class="text-muted" v-show="filter === 'version'">is between</p>
                            <div class="filter-fields version" v-show="filter === 'version'">
                                <integer-range-field :min.sync="minFilterValue" :max.sync="maxFilterValue"></integer-range-field>
                            </div>

                            <!-- Filter: Due (Date) -->
                            <p class="text-muted" v-show="filter === 'due'">is between</p>
                            <div class="filter-fields due" v-show="filter === 'due'">
                                <date-range-field :min.sync="minFilterValue" :max.sync="maxFilterValue"></date-range-field>
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
            <!-- Active Filter: Required-->
            <button type="button" v-if="params.required === 1 || params.required === 0" class="btn button-remove-filter" @click="removeFilter('required')">
                <span v-if="params.required">Compulsory Files</span>
                <span v-else>Optional Files</span>
            </button>

            <!-- Active Filter: Version -->
            <button type="button" v-if="params.version_filter_integer" class="btn button-remove-filter" @click="
                        removeFilter('version')">
                <span class="field">File Version: </span><span
                    v-if="params.version_filter_integer[0]">{{ params.version_filter_integer[0] }}</span><span v-else>~ </span><span
                    v-if="params.version_filter_integer[0] && params.version_filter_integer[1]"> - </span><span
                    v-if="params.version_filter_integer[1]">{{ params.version_filter_integer[1] }}</span><span
                    v-else> ~</span></button>

            <!-- Active Filter: Due (date) -->
            <button type="button" v-if="params.due_filter_date" class="btn button-remove-filter" @click="
                        removeFilter('due')"><span
                    class="field">Due: </span><span v-if="params.due_filter_date[0]">{{ params.due_filter_date[0] | date }}</span>
                <span v-else>~ </span><span v-if="params.due_filter_date[0] && params.due_filter_date[1]"> - </span><span
                        v-if="params.due_filter_date[1]">{{ params.due_filter_date[1] | date }}</span><span v-else> ~</span></button>

            <!-- Active Filter: Status -->
            <button type="button" v-if="params.status" class="btn button-remove-filter" @click="removeFilter('status')">
                Status: {{ params.status }}
            </button>
        </div>

    <div class="progress">
    <div class="progress-bar" role="progressbar" :aria-valuenow="receivedFilesPercentage" aria-valuemin="0"
             aria-valuemax="100" :style="'width: ' + receivedFilesPercentage + '%' + ';min-width: 2em;'">
        {{ receivedFilesPercentage }}% Received
        </div>
    </div>

    <p class="text-muted text-right small">* Maximum file size: 20MB</p>

        <div class="table-responsive">
            <!-- Files Table -->
            <table id="table-files" class="table table-standard table-hover">
                <thead>
                <tr>
                    <th class="sortable"
                        :class="{
                            'current_asc': params.sort === 'required' && params.order === 'asc',
                            'current_desc': params.sort === 'required' && params.order === 'desc',
                            }"
                        @click="changeSort('required')"
                    >
                        Required
                    </th>

                    <th class="sortable"
                        :class="{
                            'current_asc': params.sort === 'name' && params.order === 'asc',
                            'current_desc': params.sort === 'name' && params.order === 'desc',
                            }"
                        @click="changeSort('name')"
                    >
                        Name
                    </th>

                    <th class="sortable"
                        :class="{
                            'current_asc': params.sort === 'version' && params.order === 'asc',
                            'current_desc': params.sort === 'version' && params.order === 'desc',
                            }"
                        @click="changeSort('version')"
                    >
                        Version
                    </th>


                    <th class="sortable"
                        :class="{
                            'current_asc': params.sort === 'due' && params.order === 'asc',
                            'current_desc': params.sort === 'due' && params.order === 'desc',
                            }"
                        @click="changeSort('due')"
                    >
                        Due
                    </th>

                    <th class="sortable"
                        :class="{
                            'current_asc': params.sort === 'status' && params.order === 'asc',
                            'current_desc': params.sort === 'status' && params.order === 'desc',
                            }"
                        @click="changeSort('status')"
                    >
                        Status
                    </th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                <template v-for="file in files">
                    <tr>
                        <td class="fit-to-content text-center">
                            <i v-if="file.required" class="fa fa-check text-success"></i>
                            <span v-else>-</span>
                        </td>
                        <td>{{ file.name }}</td>
                        <td>{{ file.version }}</td>
                        <td>
                            <span v-if="file.due">{{ file.due | easyDate }}</span>
                            <span v-else></span>
                        </td>
                        <td>
                            <span class="file-status" :class="file.status">{{ file.status }}</span>
                        </td>
                        <td class="col-upload" v-if="canUpload">
                            <!-- History -->
                            <button type="button" class="btn btn-unstyled button-history" @click="expandFileSection(file, 'history')" :disabled="! file.uploads[0]">
                                <i class="fa fa-clock-o"></i>
                            </button>
                            <!-- Upload -->
                            <button type="button" class="btn btn-unstyled button-upload"
                                    :data-file="file.id "
                                    :disabled="file.status === 'received'"
                            >
                                <i class="fa fa-upload"></i>
                            </button>
                            <input  :id="'input-file-' + file.id "
                                    type="file"
                                    name="file"
                                    class="input-file-upload hide"
                                    @change="uploadFile(file, $event)"
                            >
                            <div class="progress" :class="{ 'disabled': file.status === 'received' }">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0"
                                     aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                    <span class="sr-only">0%</span>
                                </div>
                            </div>
                        </td>
                        <td class="col-owner fit-to-content no-wrap" v-else>
                            <!-- History -->
                            <button type="button" class="btn btn-unstyled button-history" @click="expandFileSection(file, 'history')" :disabled="! file.uploads[0]">
                                <i class="fa fa-clock-o"></i>
                            </button>
                            <!-- Reject -->
                            <button type="button" class="btn btn-unstyled button-reject" @click="expandFileSection(file, 'reject')" :disabled="file.status !== 'received'">
                                <i class="fa fa-close"></i>
                            </button>
                            <!-- Download -->
                            <a :href="'/' + file.uploads[(file.uploads.length - 1)].path" :alt="file.name + 'download link'" :download="file.name + '_v' + file.version + '_' + getUploadDate(file.uploads[(file.uploads.length - 1)])"
                               v-if="file.uploads[(file.uploads.length - 1)]">
                                <button type="button" class="btn btn-unstyled button-download"><i class="fa fa-download"></i>
                                </button>
                            </a>
                            <button type="button" class="btn btn-unstyled button-download" v-else disabled><i
                                    class="fa fa-download"></i></button>
                        </td>
                    </tr>
                    <tr class="row-details" v-show="fileExpanded(file)">
                        <td></td>
                        <td colspan="5" class="col-details">

                            <div class="confirm-reject" v-show="expandedView === 'reject'">
                                <h4>Reject {{ file.name }} v.{{ file.version }}</h4>
                                <p class="text-muted" v-if="file.uploads[0]">uploaded
                                    on {{ file.uploads[0].created_at | dateTime }}</p>
                                <form @submit.prevent="rejectFile(file)">
                                    <label>Reason</label>
                                    <div class="form-group">
                                        <textarea rows="5" class="form-control autosize" v-model="reason"></textarea>
                                    </div>
                                    <div class="text-right">
                                        <button type="button" class="btn btn-outline-grey btn-space" @click="hideDetailsSection"
                                        >Cancel</button>
                                        <button type="submit" class="btn btn-solid-red">Reject</button>
                                    </div>
                                </form>
                            </div>

                            <div class="version-history" v-show="expandedView === 'history'">
                                <h4>Version History</h4>
                                <!-- Versions Table -->
                                <table class="table table-standard">
                                    <thead>
                                    <tr>
                                        <th class="padding-even">Version</th>
                                        <th>Rejected Reason</th>
                                        <th></th>
                                    <tr>
                                    </thead>
                                    <tbody>
                                    <template v-for="(index, upload) in file.uploads">
                                        <tr>
                                            <td class="fit-to-content text-center">{{ index + 1 }}</td>
                                            <td>
                                            <span v-if="upload.rejected">
                                                {{ upload.rejected_reason }}
                                            </span>
                                                <span v-else>-</span>
                                            </td>
                                            <td class="fit-to-content">
                                                <!-- Download -->
                                                <a :href="'/' + upload.path" :alt="file.name + 'download link'" :download="file.name + '_v' + (index + 1) + '_' + getUploadDate(upload)">
                                                    <button type="button" class="btn btn-unstyled button-download"><i class="fa fa-download"></i>
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                    </template>
                                    </tbody>
                                </table>
                            </div>

                        </td>
                    </tr>
                </template>

                </tbody>
            </table>
            <div class="page-controls">
                <per-page-picker :response="response" :req-function="fetchResults"></per-page-picker>
                <paginator :response="response" :req-function="fetchResults"></paginator>
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
                        value: 'required',
                        label: 'Requirement'
                    },
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
                fileWithExpandedDetails: '',           // Holds a file object
                expandedView: '',                      // 'history', 'reject'
                reason: '',
                numReceived: ''
            }
        },
        props: ['checklist-hash', 'can-upload'],
        computed: {
            requestUrl: function () {
                return '/checklist/' + this.checklistHash + '/files';
            },
            receivedFilesPercentage: function() {
                if(! this.response.total) return 0;
                return (100 * this.numReceived / this.response.total).toFixed(2);
            }
        },
        mixins: [fetchesFromEloquentRepository],
        methods: {
            getUploadDate: function (file) {
                return moment(file.created_at).format('DDMMYYYY');
            },
            uploadFile: function (file, $event) {
                var fd = new FormData();
                var uploadedFile = $event.srcElement.files[0];

                fd.append('file', uploadedFile);

                var self = this;
                if (!self.ajaxReady) return;
                self.ajaxReady = false;
                $.ajax({
                    url: '/file/' + file.id,
                    method: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    xhr: function () {
                        var myXhr = $.ajaxSettings.xhr();
                        if (myXhr.upload) myXhr.upload.addEventListener('progress', function (e) {
                            if (e.lengthComputable) {
                                var max = e.total;
                                var current = e.loaded;
                                var progress = Math.round((current * 100) / max);
                                $('#input-file-' + file.id).siblings('.progress').children('.progress-bar').css(
                                        'width',
                                        progress + '%'
                                ).attr('aria-valuenow', progress).children('.sr-only').html(progress + '%');
                            }
                        }, false);
                        return myXhr;
                    },
                    success: function (file) {
                        self.replaceFile(file);
                        self.numReceived ++;

                        self.ajaxReady = true;
                    },
                    error: function (response) {
                        console.log(response);
                        self.ajaxReady = true;
                    }
                });
            },
            replaceFile: function (updatedFileModel) {
                var self = this;
                var index = _.indexOf(self.files, _.find(self.files, {id: updatedFileModel.id}));
                self.files.splice(index, 1, updatedFileModel);
            },
            updateProgress: function (e) {
                if (e.lengthComputable) {
                    var max = e.total;
                    var current = e.loaded;
                    var Percentage = Math.round((current * 100) / max);
                    if (Percentage >= 100) {
                        // process completed
                    }
                }
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
                        self.numReceived --;
                        self.ajaxReady = true;
                    },
                    error: function (response) {
                        console.log(response);
                        self.ajaxReady = true;
                    }
                });
            }
        },
        ready: function() {

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