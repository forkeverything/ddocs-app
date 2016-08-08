<div class="table-responsive">
    <!-- Files Table -->
    <table id="table-files" class="table table-standard">
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
                <td>@{{ file.name }}</td>
                <td>@{{ file.version }}</td>
                <td>
                    <span v-if="file.due">@{{ file.due | easyDate }}</span>
                    <span v-else></span>
                </td>
                <td>
                    <span class="file-status @{{ file.status }}">@{{ file.status }}</span>
                </td>
                @if( Auth::guest() || $checklist->user_id !== Auth::user()->id )
                    <td class="col-upload">
                        <button type="button"
                                class="btn btn-solid-green button-upload-file"
                                data-file="@{{ file.id }}"
                                :disabled="file.status === 'received'"
                        >
                            <i class="fa fa-upload"></i>
                        </button>
                        <input id="input-file-@{{  file.id }}"
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
                @else
                    <td class="col-owner fit-to-content no-wrap">
                        <!-- History -->
                        <button type="button" class="btn btn-unstyled button-history" @click="expandFileSection(file, 'history')">
                        <i class="fa fa-clock-o"></i>
                        </button>
                        <!-- Reject -->
                        <button type="button" class="btn btn-unstyled button-reject" @click="expandFileSection(file, 'reject')" :disabled="file.status !== 'received'">
                        <i class="fa fa-close"></i>
                        </button>
                        <!-- Download -->
                        <a :href="'/' + file.uploads[0].path" :alt="file.name + 'download link'" :download="file.name + '_v' + file.version + '_' + getUploadDate(file.uploads[0])"
                           v-if="file.uploads[0]">
                            <button type="button" class="btn btn-unstyled button-download"><i class="fa fa-download"></i>
                            </button>
                        </a>
                        <button type="button" class="btn btn-unstyled button-download" v-else disabled><i
                                    class="fa fa-download"></i></button>
                    </td>
                @endif
            </tr>
            <tr class="row-details" v-show="fileExpanded(file)">
                <td></td>
                <td colspan="5" class="col-details">

                    <div class="confirm-reject" v-show="expandedView === 'reject'">
                        <h4>Reject @{{ file.name }} v.@{{ file.version }}</h4>
                        <p class="text-muted" v-if="file.uploads[0]">uploaded on @{{ file.uploads[0].created_at | dateTime }}</p>
                        <form @submit.prevent="rejectFile(file)">
                            <label>Reason</label>
                            <div class="form-group">
                                <textarea rows="5" class="form-control autosize" v-model="reason"></textarea>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-outline-grey btn-space" @click="hideDetailsSection">Cancel</button>
                                <button type="submit" class="btn btn-solid-red">Reject</button>
                            </div>
                        </form>
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