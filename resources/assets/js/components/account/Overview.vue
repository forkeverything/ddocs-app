<template>
    <div id="account-overview" class="main-content">
        <div id="profile" class="account-section">
            <div class="avatar-wrap">
                <user-avatar :user="authenticatedUser"></user-avatar>
            </div>
            <div class="details">
                <h3 class="name">{{ authenticatedUser.name }}</h3>
                <p class="email">{{ authenticatedUser.email }}</p>
            </div>
        </div>
        <div id="password">
            <h5>Password</h5>
            <password-change></password-change>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'AccountOverview',
        data: function () {
            return {}
        },
        computed: {
            authenticatedUser() {
                return this.$store.state.authenticatedUser;
            }
        },
        methods: {

        },
        created() {
            this.$store.commit('setTitle', 'Account');
        }
    }
</script>