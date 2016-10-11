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
            <input type="text" class="form-control"
                   placeholder="Search..."
                   @keyup="searchFileRequest($event)"
                   v-model="search"
            >
            <div class="file-requests">
                <ul v-if="fileRequests"
                    id="list-attach-fr"
                    class="list-unstyled"
                >
                    <li v-for="fileRequest in fileRequests">
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
                <div v-if="! fileRequests"
                     class="empty"
                >
                    <p class="text-center">
                        <i class="fa fa-search"></i> No file requests found.
                        <br>
                        Search by file name, recipient email or checklist name.
                    </p>
                </div>
            </div>
        </div>
    </li>
</template>
<script>
    import RequestsMonitor from "../../requests-monitor";
    export default {
        data: function () {
            return {
                show: false,
                request: '',
                response: '',
                search: ''
            }
        },
        computed: {
            attached(){
                return this.file.file_request_id
            },
            fileRequests() {
                let data = this.response.data;
                if (!data || data.length === 0) return false;
                return data;
            }
        },
        props: ['file'],
        watch: {
            file(newFile) {
                // Find FileRequest(s) with the same name...
                if (newFile) this.fetchFileRequests(newFile.name);
            }
        },
        methods: {
            toggleDropdown(){
                if (this.attached) return;
                this.show = !this.show;
            },
            searchFileRequest(event) {
                // If no character and not backspace - do nothing...
                if (event.key.length !== 1 && event.key !== "Backspace") return;
                this.fetchFileRequests(this.search);
            },
            fetchFileRequests: _.throttle(function (search) {
                this.$http.get(`/api/file_requests/user?search=${search}`, {
                    before(xhr) {
                        if(! this) return;
                        if (this.request) RequestsMonitor.abortRequest(this.request);
                        this.request = xhr;
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.response = response.json();
                }, (response) => {
                    // error
                    console.log('Error fetching from: /api/file_requests/user');
                });
            }, 250)
        },
        mounted(){

        }
    }
</script>