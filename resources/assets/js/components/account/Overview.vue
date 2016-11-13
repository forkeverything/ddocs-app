<template>
    <div id="account-overview" class="main-content">
        <div id="profile" class="account-section">
            <div class="avatar-wrap">
                <user-avatar :user="authenticatedUser"></user-avatar>
            </div>
            <div class="details">
                <h3 class="name">
                    <editable-text-field :value="authenticatedUser.name" @on-change="updateName"></editable-text-field>
                </h3>
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
            updateName(newName) {
                let updatedUserProperties = {
                    name: newName
                };
                this.$store.commit('updateUser', updatedUserProperties);
                this.$store.dispatch('saveUserChanges', updatedUserProperties);
            }
        },
        created() {
            this.$store.commit('setTitle', 'Account');
        }
    }
</script>