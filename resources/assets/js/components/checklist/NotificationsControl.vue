<template>
    <div id="checklist-notifications-control" v-cloak>
        <div v-show="recipientNotifications">
            <button type="button"
                    class="btn btn-link"
                    @click="togglePanel"
                    v-show="! showPanel"
            >
                Turn Off Notifications
            </button>
        </div>
        <p v-else class="text-muted">Notifications & Reminders Disabled</p>
        <form id="form-notifications"
              v-show="showPanel"
              @submit.prevent="turnOffNotifications"
              class="panel panel-default animated"
              transition="fade-slide"
        >
            <div class="panel-heading">
                Turn Off Email Notifications
            </div>
            <div class="panel-body">
                <form-errors></form-errors>
                <p class="text-muted">Please enter your email address</p>
                <div class="form-group email-field">
                    <input type="email"
                           class="form-control"
                           placeholder="you@example.com"
                           name="email"
                           v-model="email"
                           :value="user.email"
                           v-if="user"
                    >
                    <input type="email"
                           class="form-control"
                           placeholder="you@example.com"
                           name="email"
                           v-model="email"
                           v-else>
                </div>
                <button type="button" class="btn btn-solid-grey btn-sm btn-space" @click="togglePanel">
                    Cancel
                </button>
                <button type="submit" class="btn btn-solid-red btn-sm">Stop reminders for this checklist</button>
            </div>
        </form>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                ajaxReady: true,
                showPanel: false,
                email: ''
            }
        },
        props: ['user', 'recipient-notifications', 'checklist-hash'],
        methods: {
            togglePanel: function () {
                this.showPanel = !this.showPanel;
            },
            turnOffNotifications: function () {
                var self = this;
                vueClearValidationErrors();
                if (!self.ajaxReady) return;
                self.ajaxReady = false;

                self.$http.post('/checklist/' + self.checklistHash + '/turn_off_notifications', {
                    email: self.email
                }).then((response) => {
                    // success
                    self.recipientNotifications = 0;
                    self.showPanel = false;
                    self.ajaxReady = true;
                }, (response) => {
                    // error
                    console.log('GET REQ Error!');
                    console.log(response);
                    self.ajaxReady = true;
                    vueValidation(response);
                });
            }
        }
    }
</script>