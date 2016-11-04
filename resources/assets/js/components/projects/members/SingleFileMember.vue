<template>
    <div class="single-member"

    >
        <span
                data-container="body"
                data-toggle="popover"
                data-placement="bottom"
                ref="avatar"
        >
            <user-avatar :user="member"
                           :class="{
                                'admin': position === 'Admin',
                                'manager': position === 'Manager',
                            }"
            ></user-avatar>
        </span>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
            };
        },
        props: ['member'],
        computed: {
            project() {
                return this.$store.state.project.data;
            },
            projectMembers(){
                return this.project.members;
            },
            position() {
                let projectMember = _.find(this.projectMembers, (projectMember) => projectMember.id === this.member.id);
                if (projectMember.pivot.admin) return 'Admin';
                if (projectMember.pivot.manager) return 'Manager';
                return 'Member';
            },
            popoverContent() {
                return `
                    <div class="avatar-popover">
                        <div class="position">
                            ${ this.position }
                        </div>
                        <div class="name text-capitalize">
                            ${ this.member.name }
                        </div>
                        <div class="email">
                            ${ this.member.email }
                        </div>
                    </div>
                `;
            }
        },
        methods: {},
        mounted() {
            this.$nextTick(() => {
                $(this.$refs.avatar).popover({
                    html: true,
                    trigger: 'hover',
                    content: this.popoverContent
                })
            });
        }
    }
</script>