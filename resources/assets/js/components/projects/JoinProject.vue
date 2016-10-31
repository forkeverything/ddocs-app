<template>
    <div id="project-join" class="container">
        <h3 v-if="error" class="text-muted text-center">
            <i class="fa fa-exclamation-triangle" style="font-size: 2em; margin: 60px 0 15px;"></i>
            <br>
            Oops! We had trouble adding you to the project. Please ask the admin to double check or resend your invitation.
        </h3>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                error: false
            }
        },
        methods: {
            joinRequest(){
                this.$http.post(`/api/projects/${ this.$route.params.project_id }/join`, {}, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.$router.push(`/projects/${ this.$route.params.project_id }`)
                }, (response) => {
                    // error
                    this.error = true;
                });
            }
        },
        mounted(){
            this.joinRequest();
        }
    }
</script>