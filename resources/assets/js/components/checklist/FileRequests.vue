<template>
    <div id="checklist-file-requests">
        <div id="fr-body">
            <div id="fr-center-pane">
                <form id="form-checklist-search" @submit.prevent="searchTerm">
                    <div class="input-group">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">Filters <span class="caret"></span>
                            </button>

                            <file-filters :filter-options="filterOptions" :min-filter-value.sync="minFilterValue"
                                          :max-filter-value.sync="maxFilterValue" :filter.sync="filter"
                                          :filter-value.sync="filterValue" :add-filter="addFilter"></file-filters>

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

                <file-active-filters :params.sync="params" :remove-filter="removeFilter"></file-active-filters>

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
                            <!-- empty spacer column-->
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
                                <file-uploader :file-request.sync="file"></file-uploader>
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
                                        <template v-if="file.uploads && file.uploads.length > 0">
                                            <tr v-for="(index, upload) in file.uploads">
                                                <td class="fit-to-content text-center">{{ index + 1 }}</td>
                                                <td>
                                            <span v-if="upload.rejected">
                                                {{ upload.rejected_reason }}
                                            </span>
                                                    <span v-else>-</span>
                                                </td>
                                                <td class="fit-to-content no-wrap col-version-controls">
                                                    <!-- Reject -->
                                                    <button v-if="isOwner"
                                                            type="button"
                                                            class="btn btn-unstyled button-reject file-buttons"
                                                            @click.stop="toggleRejectPanel"
                                                            :disabled="file.status !== 'received' || index + 1 !== file.uploads.length"
                                                    >
                                                        <i class="fa fa-close"></i> Reject
                                                    </button>
                                                    <!-- Download -->
                                                    <a :href=" awsUrl + upload.path" :alt="file.name + 'download link'">
                                                        <button type="button"
                                                                class="btn btn-unstyled button-download file-buttons"><i
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
                                                <button type="button"
                                                        class="btn file-buttons btn-unstyled button-reject" disabled><i
                                                        class="fa fa-close"></i>
                                                    Reject
                                                </button>
                                                <button type="button"
                                                        class="btn file-buttons btn-unstyled button-download" disabled>
                                                    <i class="fa fa-download"></i>
                                                    Download
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div v-show="selectedFile === file">
                                        <file-reject-panel :is-owner="isOwner" :files.sync="files"
                                                           :selected-file.sync="selectedFile"
                                                           :visible.sync="showRejectPanel"></file-reject-panel>
                                    </div>
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
                <div v-if="selectedFile">
                    <div class="header">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true"
                                @click="toggleSelectFile">&times;</span>
                        </button>
                        <h4 class="file-name">{{ selectedFile.name }}</h4>
                        <span class="date-due">{{ selectedFile.due | date }}</span>
                        <div class="single-file-buttons">
                            <file-uploader :file-request.sync="selectedFile" :with-text="true"></file-uploader>
                        </div>
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
                                <tr v-for="(index, upload) in selectedFile.uploads">
                                    <td class="fit-to-content text-center">{{ index + 1 }}</td>
                                    <td>
                                            <span v-if="upload.rejected">
                                                {{ upload.rejected_reason }}
                                            </span>
                                        <span v-else>-</span>
                                    </td>
                                    <td class="fit-to-content no-wrap col-version-controls">
                                        <!-- Reject -->
                                        <button v-if="isOwner"
                                                type="button"
                                                class="btn btn-unstyled button-reject file-buttons"
                                                @click="toggleRejectPanel"
                                                :disabled="selectedFile.status !== 'received' || index + 1 !== selectedFile.uploads.length"
                                        >
                                            <i class="fa fa-close"></i>
                                        </button>
                                        <!-- Download -->
                                        <a :href=" awsUrl + upload.path" :alt="selectedFile.name + 'download link'">
                                            <button type="button" class="btn btn-unstyled button-download file-buttons">
                                                <i
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
                                    <button type="button" class="btn file-buttons btn-unstyled button-reject" disabled>
                                        <i
                                                class="fa fa-close"></i>
                                    </button>
                                    <button type="button" class="btn file-buttons btn-unstyled button-download"
                                            disabled>
                                        <i class="fa fa-download"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <file-reject-panel :is-owner="isOwner" :files.sync="files" :selected-file.sync="selectedFile"
                                           :visible.sync="showRejectPanel"></file-reject-panel>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    const fetchesFromEloquentRepository = require('../../mixins/fetchesFromEloquentRepository');
    const replacesFileFromFilesArray = require('../../mixins/replacesFileFromFilesArray');


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
                selectedFileIndex: '',
                numReceived: '',
                showRejectPanel: false
            }
        },
        props: ['checklist-hash', 'is-owner', 'aws-url'],
        computed: {
            requestUrl: function () {
                return '/checklist/' + this.checklistHash + '/files';
            },
            receivedFilesPercentage: function () {
                if (!this.response.total) return 0;
                return (100 * this.numReceived / this.response.total).toFixed(2);
            },
            selectedFile: function () {
                return this.files[this.selectedFileIndex];
            }
        },
        mixins: [fetchesFromEloquentRepository, replacesFileFromFilesArray],
        methods: {
            getIndexOfFile: function (file) {
                return _.indexOf(this.files, _.find(this.files, {id: file.id}));
            },
            toggleSelectFile: function (file) {
                this.showRejectPanel = false;
                if (file.id) {
                    this.selectedFile = file;
                    this.selectedFileIndex = this.getIndexOfFile(file);
                } else {
                    this.selectedFileIndex = '';
                }
            },
            toggleRejectPanel: function () {
                this.showRejectPanel = !this.showRejectPanel;
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
        }
    }
</script>