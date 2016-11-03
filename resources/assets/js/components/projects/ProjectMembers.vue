<template>
    <div id="project-members">
        <ul class="list-members list-inline list-unstyled">
            <li v-for="member in members">
                <project-single-member :member="member" :admin-privileges="adminPrivileges" :manager-privileges="managerPrivileges"></project-single-member>
            </li>
            <li v-if="managerPrivileges"
                class="add-member dropdown"
                ref="dropdownAdd"
            >
                <button type="button"
                        data-toggle="dropdown"
                        role="button"
                        aria-haspopup="true"
                        aria-expanded="false"
                >
                    <i class="fa fa-plus"></i>
                </button>
                <div class="dropdown-menu">
                    <form id="form-invite-member"
                          @submit.prevent="sendInvite"
                    >
                        <form-errors></form-errors>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Email" v-model="email">
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-info" :disabled="! ajaxReady">Send</button>
                        </div>
                    </form>
                </div>
            </li>
        </ul>

        <!-- Button trigger modal -->
        <button type="button" id="btn-project-member-modal" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal-project-members">
            View Members
        </button>

        <!-- Modal -->
        <div class="modal fade" id="modal-project-members" tabindex="-1" role="dialog" aria-labelledby="project-members-modal-title">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4>Invite Member</h4>
                        <form v-if="managerPrivileges"
                              id="modal-invite-member"
                              @submit.prevent="sendInvite"
                        >
                            <form-errors></form-errors>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Email" v-model="email">
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-info" :disabled="! ajaxReady">Send</button>
                            </div>
                        </form>
                        <h4>Members List</h4>
                        <ul class="list-members-modal list-unstyled">
                            <li v-for="member in members">
                                <div class="main">
                                    <div class="avatar">
                                        <member-avatar :member="member"
                                                       :class="{
                                                            'admin': member.pivot.admin,
                                                            'manager': member.pivot.manager
                                                       }"
                                        ></member-avatar>
                                    </div>
                                    <div class="details">
                                        <div class="name">
                                            {{ member.name }}
                                        </div>
                                        <div class="email">
                                            {{ member.email }}
                                        </div>
                                    </div>
                                </div>
                                <member-actions v-if="adminPrivileges || managerPrivileges"
                                                :admin-privileges="adminPrivileges"
                                                :manager-privileges="managerPrivileges"
                                                :member="member"
                                ></member-actions>
                            </li>
                        </ul>
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
                ajaxReady: true,
                email: ''
            }
        },
        computed: {
            project() {
                return this.$store.state.project.data;
            },
            members(){
                return this.project.members;
            },
            authenticatedUser(){
                return this.$store.state.authenticatedUser;
            },
            adminPrivileges(){
                return !! _.find(this.members, (member) => {
                    return member.pivot.admin && member.id === this.authenticatedUser.id;
                });
            },
            managerPrivileges(){
                if(this.adminPrivileges) return true;
                return !! _.find(this.members, (member) => {
                    return member.pivot.manager && member.id === this.authenticatedUser.id;
                });
            }
        },
        methods: {
            sendInvite(){
                vueClearValidationErrors();
                if (!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.post(`/api/projects/${ this.project.id }/invite`, {
                    email: this.email
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.ajaxReady = true;
                    this.email = '';
                    this.$nextTick(() => $(this.$refs.dropdownAdd).removeClass('open'));
                }, (response) => {
                    // error
                    console.log('error inviting member.');
                    vueValidation(response);
                    this.ajaxReady = true;
                });
            }
        }
    }
</script>