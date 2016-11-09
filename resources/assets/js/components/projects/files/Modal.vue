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
                                <h3 class="file-name">
                                    <pf-status-circle v-if="file" :project-file="file"></pf-status-circle>
                                    <span class="name-wrap">
                                    <editable-text-field v-model="file.name"
                                                         @on-change="updateFileName"></editable-text-field>
                                </span>
                                </h3>
                                <div id="pfm-mobile-actions">
                                    <pf-modal-actions :file="file"
                                                      :attached="attached"
                                                      @go-to-checklist="goToChecklist"
                                                      @set-file="setFile"
                                                      @update-file-request="updateFileRequest"
                                                      @switch-view="switchView"
                                                      @hide="hide"
                                    ></pf-modal-actions>
                                </div>
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
                                <component :is="currentView"
                                           :file="file"
                                           @go-to-checklist="goToChecklist"
                                           @detach-request="detachRequest"
                                >

                                </component>
                            </div>
                            <div id="pfm-main-actions">
                                <pf-modal-actions :file="file"
                                                  :attached="attached"
                                                  @go-to-checklist="goToChecklist"
                                                  @set-file="setFile"
                                                  @update-file-request="updateFileRequest"
                                                  @switch-view="switchView"
                                                  @hide="hide"
                                ></pf-modal-actions>
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
                file: ''
            }
        },
        props: [],
        computed: {
            project() {
                return this.$store.state.project.data;
            },
            attached(){
                return this.file.file_request
            }
        },
        methods: {
            setFile(file) {
                this.file = file;
            },
            updateFileName() {
                vueGlobalEventBus.$emit('update-project-file', {
                    id: this.file.id,
                    name: this.file.name
                });
            },
            updateFileRequest(fileRequest) {
                vueGlobalEventBus.$emit('update-project-file', {
                    id: this.file.id,
                    file_request: fileRequest
                });
            },
            detachRequest() {
                this.$http.post(`/api/projects/${ this.project.id }/files/${ this.file.id }/attach_fr`, {
                    'file_request_hash': null
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.switchView('pfm-project-view');
                    this.$nextTick(() => {
                        this.updateFileRequest(null);
                        this.file = response.json();
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
                if(! this.file.file_request) return;
                window.open('/c/' + this.file.file_request.checklist_hash, '_blank');
            },
            fetchProjectFile(fileId){
                this.$http.get(`/api/projects/${ this.project.id }/files/${ fileId }`, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.file = response.json();
                    this.$nextTick(() => {
                        this.loading = false;
                    });
                }, (response) => {
                    // error
                    console.log('Error fetching from: `/api/projects`');
                });
            }
        },
        created() {
            vueGlobalEventBus.$on('view-project-file', (file) => {
                this.loading = true;
                this.file = '';
                this.fetchProjectFile(file.id);
                this.switchView('pfm-project-view');
                this.$nextTick(() => {
                    $(this.$refs.modal).modal('show');
                });
            });
            vueGlobalEventBus.$on('update-file-member', (fileId, member, assign) => {
                if(this.file.id !== fileId) return;
                if(! this.file.members) Vue.set(this.file, 'members', []);
                if(assign) {
                    this.file.members.push(member);
                } else {
                    let index = _.indexOf(this.file.members, _.find(this.file.members, (fileMember) => fileMember.id === member.id));
                    this.file.members.splice(index, 1);
                }
            });
            vueGlobalEventBus.$on('removed-project-member', (member) => {
                if(! this.file.members) return;
                let targetMember = _.find(this.file.members, (fileMember) => {
                    return fileMember.id === member.id;
                });
                if(targetMember) this.file.members.splice(_.indexOf(this.file.members, targetMember), 1);
            });
            vueGlobalEventBus.$on('update-project-file', (file) => {
                if (file.id !== this.file.id) return;
            for (let prop in file) {
                if (file.hasOwnProperty(prop)) {
                    this.file[prop] = file[prop];
                }
            }
        });
        },
        mounted() {

        },
        beforeDestroy(){
            vueGlobalEventBus.$off('view-project-file');
        }
    }
</script>