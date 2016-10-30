<template>
    <div class="date-wrap">
        <div v-if="checklistBelongsToUser">
            <date-picker v-model="date"
                         :formatted="true"
                         :button-only="true"
                         :carbon="true"
                         @on-change="save"
            ></date-picker>
        </div>
        <div v-if="! checklistBelongsToUser">
            <smart-date v-if="fileRequest.due" :date="fileRequest.due"></smart-date>
            <span v-if="! fileRequest.due">--</span>
        </div>
    </div>
</template>
<script>
export default {
    data: function(){
        return {
            date: '',
            request: ''
        }
    },
    props:['checklist-belongs-to-user', 'file-request', 'index'],
    methods: {
        save(newDate) {
            this.$http.put('/api/file_requests/' + this.fileRequest.hash, {
                due: newDate
            }, {
                before(xhr) {
                    if(this.request) RequestsMonitor.abortRequest(this.request);
                    this.request = xhr;
                    RequestsMonitor.pushOntoQueue(xhr);
                }
            }).then((response) => {
                // success
                this.$emit('update-file-request', response.json(), this.index);
            }, (response) => {
                // error
                console.log('error updating due date');
                console.log(response);

            })
        }
    },
    mounted() {
        this.date = this.fileRequest.due;
    }
}
</script>