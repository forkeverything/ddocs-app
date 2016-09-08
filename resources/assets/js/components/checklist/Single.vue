<template>
    <div id="checklist-single" class="container">
        <div id="page-fixed-top" class="container-no-gutter">


            <div id="join-offer" v-if="! checklistBelongsToUser && ! checklist.invitation_claimed">
                <h3 class="text-center">Help {{ checklist.user.name }} Out!</h3>
                <p class="text-center">Join Files Collector and give {{ checklist.user.name }} 10 free
                    credits.
                    You'll also get 5 bonus credits for being an awesome friend.
                    <br>
                    Think of it as our way of saying thanks for trying us out!
                </p>
                <a href="/register?invite_key={{ $checklistHash }}"><p class="text-center">Sign Me Up</p></a>
            </div>

            <h3>
                <strong>
                    <span class="text-capitalize">{{ checklist.name }}</span>
                </strong>
            </h3>

            <div class="recipients">
                <span class="span-to">To: </span>
                <ul class="recipients-list list-unstyled list-inline">
                    <li v-for="recipient in checklist.recipients">{{ recipient.email }}</li>
                </ul>
            </div>

            <p v-if="checklist.description">
                {{ checklist.description }}
            </p>

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
                 class="table-header"
                 v-show="selectedFile"
            >
                <span class="file-name" v-if="selectedFile">{{ selectedFile.name }}</span>
                <ul class="list-menu-items list-inline list-unstyled">
                    <li class="menu-item"><a href="#" @click.prevent="showRejectModal"
                                             :class="{ 'disabled': ! canRejectFile }"><i
                            class="icon reject fa fa-close"></i>Reject</a></li>
                    <li class="menu-item"><a href="#"><i class="icon history fa fa-clock-o"></i>History</a></li>
                    <li class="menu-item"><a href="#"><i class="icon rename fa fa-edit"></i>Rename</a></li>
                    <li class="menu-item"><a href="#"><i class="icon delete fa fa-trash-o"></i>Delete</a></li>
                </ul>
            </div>

            <ul id="files-header"
                class="list-unstyled list-inline table-header"
                v-show="! selectedFile"
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
                <li class="column col-upload header-column">
                    <!-- empty spacer column-->
                </li>
            </ul>
        </div>

        <div id="page-scroll-content">
            <ul id="files-list" class="list-unstyled">
                <li class="single-file"
                    v-for="(index, file) in files"
                    @focus="selectFile(index)"
                    tabindex="1"
                    :class="{ 'is-selected': file === selectedFile }"
                    @keydown.up="selectFile(index - 1)"
                    @keydown.down="selectFile(index + 1)"
                >
                    <div class="column col-file content-column file-status" :class="file.status">
                        <i class="fa fa-file-o"></i>
                    </div>
                    <div class="column col-name content-column">
                        <!-- Download -->
                        <a v-if="file.uploads.length > 0" :href=" awsUrl + file.uploads[(file.uploads.length - 1)].path" :alt="file.name + 'download link'">
                            {{ file.name }}
                        </a>
                        <span v-else>{{ file.name }}</span>
                    </div>
                    <div class="column col-due content-column">
                        <span class="date" v-if="file.due">{{ file.due | date }}</span>
                        <span v-else>--</span>
                    </div>
                    <div class="column col-upload content-column">
                        <file-uploader :file-request.sync="file" :with-text="true"></file-uploader>
                    </div>
                </li>
            </ul>
        </div>
        <file-reject-modal></file-reject-modal>
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
                files: [],
                numReceived: '',
                selectedFileIndex: ''
            }
        },
        computed: {
            selectedFile: function () {
                return this.files[this.selectedFileIndex];
            },
            checklistBelongsToUser: function () {
                return this.user.id === this.checklist.user_id;
            },
            requestUrl: function () {
                return '/checklist/' + this.checklistHash + '/files';
            },
            canRejectFile: function () {
                if (!this.selectedFile) return false;
                return this.selectedFile.status === 'received';
            },
            receivedFilesPercentage: function () {
                if (!this.response.total) return 0;
                return (100 * this.numReceived / this.response.total).toFixed(2);
            }
        },
        props: ['aws-url', 'user', 'checklist', 'checklist-hash'],
        mixins: [fetchesFromEloquentRepository],
        methods: {
            getFileIndex: function (file) {
                return _.indexOf(this.files, _.find(this.files, {id: file.id}));
            },
            selectFile: function (index) {
                if (!this.files[index]) return;  // file doesn't exist
                this.selectedFileIndex = index;
                this.$nextTick(() => {
                    this.focusOnFile(this.selectedFileIndex);
                });
            },
            focusOnFile: function (index) {
                $('.single-file')[index].focus();
            },
            showRejectModal: function () {
                if (this.canRejectFile) vueGlobalEventBus.$emit('show-reject-modal', this.selectedFile);
            }
        },
        ready: function () {

            var self = this;

            // Parse out our request params
            self.$watch('response', (response) => {
                self.files = $.map(_.omit(response.data, 'query_parameters'), (file, index) => {
                    return file;
                });
                self.numReceived = self.params.num_received_files;
            });

            // Click event bind - Unselect file if we didn't click inside list or within select menu
            // Elements that even if we click on, we don't want to lose selectedFile
            let focusContainers = [
                $('#selected-file-menu'),
                $('#files-list'),
                $('.reject-modal')
            ];
            $(document).on('click', (e) => {
                let clickedInside = false;
                for (var i = 0; i < focusContainers.length; i++) {
                    if (focusContainers[i].is(e.target) || focusContainers[i].has(e.target).length !== 0) {
                        clickedInside = true;
                        break;
                    }
                }
                if (!clickedInside) self.selectedFileIndex = '';
            });

            // when we reject a file updated it...
            vueGlobalEventBus.$on('rejected-file', (updatedFileModel) => {
                let index = self.getFileIndex(updatedFileModel);
                self.$set('files[' + index + ']', updatedFileModel);
            });
        }
    };
</script>