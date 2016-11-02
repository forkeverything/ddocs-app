<template>
<div class="member-avatar"
     @mouseover="revealContact"
     @mouseleave="showContact = false"
     ref="avatar"
>
    <div class="initials">{{ initials }}</div>
    <div class="contact-card"
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
    data: function(){
        return {
            showContact: false,
            cardLeftPosition: '0'
        };
    },
    props: ['member', 'admin-privileges', 'manager-privileges'],
    computed: {
        position() {
            if(this.member.pivot.admin) return 'Admin';
            if(this.member.pivot.manager) return 'Manager';
            return 'Member';
        },
        initials() {
            let initials = '';
            let arr = this.member.name.split(' ');
            initials += arr[0].charAt(0);
            if(arr[1]) initials += arr[1].charAt(0);
            return initials.toUpperCase();
        }
    },
    methods: {
        revealContact() {
            let $card = $(this.$refs.card);
            let $avatar = $(this.$refs.avatar);
            let cardWidth = $card.width() + 20;
            let avatarLeft = $avatar.offset().left + 15;
            let windowWidth = $(window).width();
            this.cardLeftPosition = cardWidth + avatarLeft > windowWidth ? `${ windowWidth - cardWidth - avatarLeft  }px` : '0';
            this.$nextTick(() => this.showContact = true);
        }
    }
}
</script>