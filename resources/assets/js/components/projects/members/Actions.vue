<template>
    <div class="member-actions">
        <ul class="list-inline list-unstyled list-actions">
            <li v-if="adminPrivileges && isManager">
                <button type="button"
                        class="btn btn-default btn-sm"
                        @click="defineManager(0)"
                >
                    <i class="fa fa-arrow-down"></i> Manager
                </button>
            </li>
            <li v-if="adminPrivileges && isNormalMember">
                <button type="button"
                        class="btn btn-success btn-sm"
                        @click="defineManager(1)"
                >
                    <i class="fa fa-arrow-up"></i> Manager
                </button>
            </li>
            <li v-if="managerPrivileges && ! isAdmin">
                <button type="button"
                        class="btn btn-danger btn-sm"
                        @click="remove"
                >
                    <i class="fa fa-minus"></i> Member
                </button>
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
        props: ['member', 'admin-privileges', 'manager-privileges'],
        computed: {
            project() {
                return this.$store.state.project.data;
            },
            isAdmin() {
                return this.member.pivot.admin;
            },
            isManager(){
                return this.member.pivot.manager;
            },
            isNormalMember() {
                return !this.member.pivot.admin && !this.member.pivot.manager;
            }
        },
        methods: {
            defineManager(isManager) {
                if (!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.post(`/api/projects/${ this.project.id }/manager`, {
                    manager: isManager,
                    'user_id': this.member.id
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.$store.commit('project/DEFINE_MEMBER_MANAGER', {
                        id: this.member.id,
                        manager: isManager
                    });
                    this.ajaxReady = true;
                }, (response) => {
                    // error
                    console.log("error defining manager.");
                    this.ajaxReady = true;
                });
            },
            remove(){
                if (!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.delete(`/api/projects/${ this.project.id }/members/${ this.member.id }`, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((res) => {
                    this.$store.commit('project/REMOVE_MEMBER', {
                        id: this.member.id
                    });
                    vueGlobalEventBus.$emit('removed-project-member', this.member);
                    this.ajaxReady = true;
                }, (res) => {
                    console.log('error removing member.');
                    this.ajaxReady = true;
                });
            }
        }
    }
</script>