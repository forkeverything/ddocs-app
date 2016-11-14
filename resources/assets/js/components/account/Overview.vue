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
            <h4>Password</h4>
            <password-change></password-change>
        </div>
        <div id="account-credits">
            <h4>Credits</h4>
            <p class="remaining">
                {{ authenticatedUser.credits }}
            </p>
            <div class="row">
                <div class="col-sm-6">
                    <p class="text-muted">
                        Each checklist costs 1 credit to make. You automatically score extra credits when your recipients create an account with us.
                        Also, every month your account gets replenished up to 5 credits.
                    </p>
                </div>
            </div>
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