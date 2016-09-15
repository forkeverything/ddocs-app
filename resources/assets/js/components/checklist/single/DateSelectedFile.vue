<template>
<div id="selected-file-date">
    <file-date-input :date="fileRequest.due"></file-date-input>
    <div class="loader" v-show="! ajaxReady">
        <i class="fa fa-spinner fa-pulse fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
</div>
</template>
<script>
export default {
    data: function(){
        return {
            ajaxReady: true
        }
    },
    props: ['file-request'],
    methods: {
        updateDueDate(date) {
            var self = this;
            if(!self.ajaxReady) return;
            self.ajaxReady = false;

            self.$http.put('/fr/' + this.fileRequest.hash, {
                    due: date
                }).then((response) => {
                	// success
                    self.fileRequest = JSON.parse(response.data);
                	self.ajaxReady = true;
                },(response) => {
                	// error
                    console.log('GET REQ Error!');
                    console.log(response);
                    self.ajaxReady = true;
                    vueValidation(response);
                })
        }
    },
    ready() {
        vueGlobalEventBus.$on('updated-date', (newDate) => {
            console.log('got new date');
            this.updateDueDate(newDate);
        })
    }
}
</script>