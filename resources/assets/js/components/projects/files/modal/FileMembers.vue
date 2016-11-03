<template>
    <div id="pf-members">
        <ul class="list-members list-inline list-unstyled">
            <li class="add-member dropdown"
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
                <div id="dropdown-assign-member" class="dropdown-menu" @click.stop="">
                    <ul class="list-unstyled">
                        <li v-for="member in projectMembers">
                            <div class="main">
                                <div class="avatar">
                                    <member-avatar :member="member"
                                                   :class="{
                                                        'admin': getPosition(member) === 'Admin',
                                                        'manager': getPosition(member) === 'Manager',
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
                            <button type="button"
                                    class="btn-assign btn btn-text"
                                    :class="{
                                        'active': alreadyAssigned(member)
                                    }"
                                    @click="assign(member)"
                            >
                                <i class="fa fa-check"></i>
                            </button>
                        </li>
                    </ul>
                </div>
            </li>
            <li v-for="member in fileMembers">
                <pf-single-member :member="member"></pf-single-member>
            </li>
        </ul>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                ajaxReady: true
            }
        },
        props: ['project-file'],
        computed: {
            project() {
                return this.$store.state.project.data;
            },
            projectMembers(){
                return this.project.members;
            },
            authenticatedUser(){
                return this.$store.state.authenticatedUser;
            },
            fileMembers() {
                return this.projectFile.members;
            }
        },
        methods: {
            getPosition(member) {
                let projectMember = _.find(this.projectMembers, (projectMember) => projectMember.id === member.id);
                if (projectMember.pivot.admin) return 'Admin';
                if (projectMember.pivot.manager) return 'Manager';
                return 'Member';
            },
            alreadyAssigned(projectMember) {
                return !!_.find(this.fileMembers, (member) => member.id === projectMember.id);
            },
            assign(projectMember) {
                if(!this.ajaxReady) return;
                this.ajaxReady = false;
                let assign = ! this.alreadyAssigned(projectMember);
                this.$http.post(`/api/projects/${ this.project.id }/files/${ this.projectFile.id }/assign`, {
                    'user_id': projectMember.id,
                    assign: assign
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    vueGlobalEventBus.$emit('update-file-member', this.projectFile.id, projectMember, assign);

                    this.ajaxReady = true;
                },(response) => {
                    // error
                    console.log('error assigning member to project file.');
                    this.ajaxReady = true;
                });
            }
        }
    }
</script>