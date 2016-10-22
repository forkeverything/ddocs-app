<template>
    <div id="project-file-modal">
        <div class="modal" tabindex="-1" role="dialog" ref="modal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <rectangle-loader :loading="loading"></rectangle-loader>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <div class="main">
                            <div class="content">
                                <h3 class="file-name"><i class="fa fa-file-o"></i> <span class="name-wrap"><editable-text-field
                                        v-model="file.name"></editable-text-field></span></h3>
                                <ul class="nav nav-tabs">
                                    <li role="presentation"
                                        :class="{
                                                    'active': currentView === 'pfm-project-view'
                                                }"
                                    >
                                        <a href="#"
                                           @click.prevent="switchView('pfm-project-view')"
                                        >
                                            Project
                                        </a>
                                    </li>
                                    <li role="presentation"
                                        :class="{
                                                    'active': currentView === 'pfm-request-view'
                                                }"
                                    >
                                        <a href="#"
                                           @click.prevent="switchView('pfm-request-view')"
                                           :class="{
                                                disabled: ! attached
                                           }"
                                        >
                                            Request
                                        </a>
                                    </li>
                                </ul>


                                <div class="dropdown mobile-actions">
                                    <a class="btn-dropdown clickable" data-toggle="dropdown" aria-haspopup="true"
                                       aria-expanded="false">•••</a>
                                    <ul class="dropdown-menu list-unstyled dropdown-menu-right">
                                        <li class="heading"><h5>Request</h5></li>
                                        <li v-if="! attached">
                                            <a class="clickable"
                                               data-toggle="tooltip"
                                               data-placement="left"
                                               title="link file request"
                                            >
                                                <i class="fa fa-link"></i>Attach
                                            </a>
                                        </li>
                                        <li v-if="attached">
                                            <a class="clickable" @click.prevent="detachRequest">
                                                <i class="fa fa-unlink"></i>Detach
                                            </a>
                                        </li>
                                        <li>
                                            <a class="clickable" :disabled="! attached" @click="goToChecklist"><i
                                                    class="fa fa-list"></i>Checklist</a>
                                        </li>
                                        <li class="heading"><h5>Project</h5></li>
                                        <li>
                                            <a class="clickable">
                                                <i class="fa fa-upload"
                                                   data-toggle="tooltip"
                                                   data-placement="left"
                                                   title="Add internal file"
                                                   :disabled="! canUpload"
                                                >
                                                </i>Upload
                                            </a>
                                        </li>
                                        <li><a class="clickable"><i class="fa fa-trash"></i>Delete</a></li>
                                    </ul>
                                </div>
                                <component :is="currentView" :file="file" @go-to-checklist="goToChecklist"></component>
                            </div>
                            <div class="actions">
                                <ul id="list-pf-modal-actions" class="list-unstyled">
                                    <li><h5>Request</h5></li>

                                    <li v-if="! attached" class="attach-wrapper">
                                        <a class="btn btn-primary btn-sm" @click.prevent.stop="toggleAttachFRMenu">
                                            <i class="fa fa-link"></i>Attach
                                        </a>
                                        <attach-fr-dropdown v-if="attachFileRequestMenu"
                                                            :project-id="projectId"
                                                            :file="file"
                                                            @attached-request="attachedRequest"
                                                            @toggle-attach-fr-menu="toggleAttachFRMenu"
                                        >
                                        </attach-fr-dropdown>
                                    </li>

                                    <li v-if="attached">
                                        <a class="btn btn-primary btn-sm" @click.prevent="detachRequest">
                                            <i class="fa fa-unlink"></i>Detach
                                        </a>
                                    </li>
                                    <li>
                                        <a class="btn btn-primary btn-sm" :disabled="! attached" @click="goToChecklist"><i
                                                class="fa fa-list"></i>Checklist</a>
                                    </li>
                                    <li><h5>Project</h5></li>
                                    <li><a class="btn btn-primary btn-sm"
                                           :disabled="! canUpload"
                                    >
                                        <i class="fa fa-upload"
                                           data-toggle="tooltip"
                                           data-placement="right"
                                           title="Add internal file"
                                        ></i>Upload
                                    </a>
                                    </li>
                                    <li><a class="btn btn-primary btn-sm"><i class="fa fa-trash"></i>Delete</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                loading: true,
                ajaxReady: true,
                currentView: 'pfm-project-view',
                file: '',
                attachFileRequestMenu: false
            }
        },
        computed: {
            attached(){
                return this.file.file_request
            },
            canUpload() {
                return !this.attached;
            }
        },
        props: ['project-id'],
        methods: {
            toggleAttachFRMenu() {
                this.attachFileRequestMenu = ! this.attachFileRequestMenu
            },
            attachedRequest(file) {
                // update our project file to include the file request
                this.file = file;
                this.$nextTick(() => {
                    this.switchView('pfm-request-view');
                });
            },
            detachRequest() {
                this.$http.post(`/api/projects/${ this.projectId }/files/${ this.file.id }/attach_fr`, {
                    'file_request_hash': null
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.$emit('attached-request', response.json());
                    this.file = response.json();
                    this.$nextTick(() => {
                        this.switchView('pfm-project-view');
                    });
                    this.ajaxReady = true;
                }, (response) => {
                    // error
                    console.log("error posting to: `/projects/${ this.file.project_id }/attach`");
                    this.ajaxReady = true;
                });
            },
            switchView(view) {
                if (view === 'pfm-request-view' && !this.attached) return;
                this.currentView = view;
            },
            hide(){
                $(this.$refs.modal).modal('hide');
            },
            goToChecklist() {
                $(this.$refs.modal).modal('hide');
                router.push('/c/' + this.file.file_request.checklist_hash);
            },
            fetchProjectFile(fileId){
                this.$http.get(`/api/projects/${ this.projectId }/files/${ fileId }`, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.loading = false;
                    this.file = response.json();
                }, (response) => {
                    // error
                    console.log('Error fetching from: `/api/projects`');
                });
            }
        },
        mounted() {
            vueGlobalEventBus.$on('view-project-file', (file) => {
                this.loading = true;
                this.file = '';
                this.fetchProjectFile(file.id);
                this.switchView('pfm-project-view');
                this.$nextTick(() => {
                    $(this.$refs.modal).modal('show');
                });
            })
        }
    }
</script>