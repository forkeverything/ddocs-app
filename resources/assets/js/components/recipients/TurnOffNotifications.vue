<template>
<div id="notifications-off" class="container">
    <br>
    <br>
    <div v-if="msg"
         class="message alert col-md-8 col-md-offset-2 text-center"
         :class="{
            'alert-success': msg.success,
            'alert-danger': ! msg.success
         }"
         role="alert"
    >
        <span v-html="msg.body"></span>
    </div>
</div>
</template>
<script>
export default {
    data: function(){
        return {
            msg: ''
        }
    },
    methods: {
        turnOffNotifications() {
            this.$http.post(`/api/recipients/${ this.$route.params.recipient_hash }/turn_off_notifications`, {}, {
                before(xhr) {
                    RequestsMonitor.pushOntoQueue(xhr);
                }
            }).then((response) => {
                // success
                this.msg = {
                    body: 'Successfully turned off notifications for checklist.',
                    success: true
                }
            },(response) => {
                // error
                console.log("error posting to: `/api/recipients/turn_off_notifications`");
                this.msg = {
                    body: "<strong>Sorry!</strong> We couldn't find that recipient. Please check the link or URL and try again.",
                    success: false
                };
            });
        }
    },
    mounted(){
        this.turnOffNotifications();
    }
}
</script>