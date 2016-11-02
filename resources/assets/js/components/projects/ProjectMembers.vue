<template>
    <div id="project-members">
        <ul class="list-members list-inline list-unstyled">
            <li v-for="member in members"
                class="single-member"
                :class="{
                'admin': member.pivot.admin,
                'manager': member.pivot.manager
            }"
            >
                <member-avatar :member="member" :admin-privileges="adminPrivileges" :manager-privileges="managerPrivileges"></member-avatar>
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