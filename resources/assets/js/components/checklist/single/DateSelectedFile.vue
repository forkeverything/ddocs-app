<template>
    <div id="selected-file-date">

        <button type="button" class="btn btn-due-date" @click="pickDate" :class="{ filled: fileRequest.due }">
            <span class="icon">
                <i class="fa fa-calendar" v-if="ajaxReady"></i>
                <i class="fa fa-spinner fa-pulse fa-fw" v-else></i>
            </span>
            <span class="date" v-show="fileRequest.due">{{ formattedDate }}</span>
            <span v-else class="date">Due Date</span>
            <input type="text"
                   v-model="newDate"
                   v-datepicker
                   @keydown.delete.prevent="removeDate"
                   tabindex="-1"
                   v-el:input
            >
        </button>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                ajaxReady: true,
                newDate: ''
            }
        },
        props: ['file-request'],
        computed: {
            formattedDate() {
                if (!this.fileRequest.due) return;
                return Vue.filter('smartDate')(this.fileRequest.due);
            }
        },
        watch: {
            newDate(date) {
                this.updateDueDate(date);
            }
        },
        methods: {
            pickDate() {
                $(this.$els.input).datepicker('show');
            },
            removeDate () {
                this.updateDueDate('');
                $(this.$els.input).datepicker('hide');
            },
            updateDueDate(date) {
                var self = this;
                if (!self.ajaxReady) return;
                self.ajaxReady = false;

                self.$http.put('/fr/' + this.fileRequest.hash, {
                    due: date
                }).then((response) => {
                    // success
                    self.fileRequest = JSON.parse(response.data);
                    self.ajaxReady = true;
                }, (response) => {
                    // error
                    console.log('GET REQ Error!');
                    console.log(response);
                    self.ajaxReady = true;

                })
            }
        },
        ready() {

        }
    }
</script>