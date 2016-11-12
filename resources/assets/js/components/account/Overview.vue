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
            <a href="#" class="btn-text" @click.prevent="togglePasswordForm" v-show="! passwordForm">Change Password</a>
            <form id="form-change-password" v-show="passwordForm" @submit.prevent="savePassword">
                <form-errors></form-errors>
                <div class="form-group">
                    <label for="field-current-password">Current</label>
                    <input type="password" id="field-current-password" class="form-control" v-model="currentPassword">
                </div>
                <div class="form-group">
                    <label for="field-new-password">New</label>
                    <input type="password" id="field-new-password" class="form-control" v-model="newPassword">
                </div>
                <div class="form-group">
                    <label for="field-confirm-password">Confirm New</label>
                    <input type="password" id="field-confirm-password" class="form-control"
                           v-model="newPasswordConfirmation">
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-default btn-space" @click="togglePasswordForm">Cancel</button>
                    <button type="submit" class="btn btn-primary" :disabled="! canSavePassword"><span v-if="ajaxReady">Save</span><span
                            v-if="! ajaxReady">Saving...</span></button>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'AccountOverview',
        data: function () {
            return {
                ajaxReady: true,
                passwordForm: false,
                currentPassword: '',
                newPassword: '',
                newPasswordConfirmation: ''
            }
        },
        computed: {
            authenticatedUser() {
                return this.$store.state.authenticatedUser;
            },
            canSavePassword() {
                return this.currentPassword && this.newPassword && (this.newPassword === this.newPasswordConfirmation);
            }
        },
        methods: {
            togglePasswordForm() {
                this.currentPassword = '';
                this.newPassword = '';
                this.newPasswordConfirmation = '';
                return this.passwordForm = ! this.passwordForm;
            },
            savePassword(){
                vueClearValidationErrors();
                if (!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.post('/api/account/password', {
                    current_password: this.currentPassword,
                    new_password: this.newPassword,
                    new_password_confirmation: this.newPasswordConfirmation
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.ajaxReady = true;
                    this.togglePasswordForm();
                }, (response) => {
                    // error
                    console.log("error posting to: /api/account/password");
                    this.ajaxReady = true;
                    vueValidation(response);
                });
            }
        },
        created() {
            this.$store.commit('setTitle', 'Account');
        }
    }
</script>