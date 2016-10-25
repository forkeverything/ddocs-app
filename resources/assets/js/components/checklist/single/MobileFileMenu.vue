<template>
    <div id="mobile-file-menu">
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
                       :class="{ 'disabled': ! canRejectFile }"
                    >
                        <i class="icon reject fa fa-close"></i>Reject
                    </a>
                </li>
                <li class="dropdown visible-xs-inline">
                    <a id="select-menu-more" @click.prevent="toggleDropdownMenu"
                       data-toggle="dropdown"
                       role="button" aria-haspopup="true"
                       aria-expanded="false">
                        More
                        <span class="caret"></span>
                    </a>
                    <ul ref="dropdown-menu" class="dropdown-menu dropdown-menu-right" aria-labelledby="select-menu-more">
                        <li class="menu-item">
                            <a :href="'/file_requests/' + selectedFileRequest.hash + '/history'"
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
                        :href="'/file_requests/' + selectedFileRequest.hash + '/history'"
                        :class="{'disabled': ! selectedFileRequest.latest_upload }"><i
                        class="icon history fa fa-clock-o"></i>History</a></li>
                <li class="menu-item hidden-xs"><a href="#" @click.prevent="showDeleteModal"><i
                        class="icon delete fa fa-trash-o"></i>Delete</a></li>
            </ul>
    </div>
</template>
<script>
export default {
    data: function(){
        return {

        }
    },
    props: ['selected-file-request', 'show-delete-modal', 'upload-selected', 'show-reject-modal', 'can-reject-file'],
    methods: {
        toggleDropdownMenu() {
            $(this.$refs.dropdownMenu).toggle();
        },
        hideDropdownMenu() {
            $(this.$refs.dropdownMenu).hide();
        }
    },
    mounted() {
        let $el = $(this.$refs.dropdownMenu);
        $(document).on('click', (e) => {
            if(! $el.is(e.target) && $el.has(e.target).length === 0) {
                this.hideDropdownMenu();
            }
        });
    }
}
</script>