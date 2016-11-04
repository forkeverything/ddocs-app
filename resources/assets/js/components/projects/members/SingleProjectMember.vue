<template>
    <div class="single-member"
         @mouseover="revealContact"
         @mouseleave="showContact = false"
         ref="container"
    >
        <user-avatar :user="member"
                     :class="{
                            'admin': member.pivot.admin,
                            'manager': member.pivot.manager
                       }"
        ></user-avatar>

        <div class="avatar-popover"
             ref="card"
             :class="{
            'visible': showContact
         }"
             :style="{
            'left': cardLeftPosition
         }"
        >
            <div class="content popup">
                <div class="position">
                    {{ position }}
                </div>
                <div class="name text-capitalize">
                    {{ member.name }}
                </div>
                <div class="email">
                    {{ member.email }}
                </div>
                <member-actions v-if="adminPrivileges || managerPrivileges"
                                :admin-privileges="adminPrivileges"
                                :manager-privileges="managerPrivileges"
                                :member="member"
                ></member-actions>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                showContact: false,
                cardLeftPosition: '0'
            };
        },
        props: ['member', 'admin-privileges', 'manager-privileges'],
        computed: {
            position() {
                if (this.member.pivot.admin) return 'Admin';
                if (this.member.pivot.manager) return 'Manager';
                return 'Member';
            }
        },
        methods: {
            revealContact() {
                let $card = $(this.$refs.card);
                let $container = $(this.$refs.container);
                let cardWidth = $card.width() + 20;
                let containerLeft = $container.offset().left + 15;
                let windowWidth = $(window).width();
                this.cardLeftPosition = cardWidth + containerLeft > windowWidth ? `${ windowWidth - cardWidth - containerLeft  }px` : '0';
                this.$nextTick(() => this.showContact = true);
            }
        }
    }
</script>