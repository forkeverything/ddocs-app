<template>
    <div id="selected-file-date">

        <button type="button" class="btn btn-due-date" @click="pickDate" :class="{ filled: fileRequest.due }"
                v-if="user">
            <span class="icon">
                <i class="fa fa-calendar" v-if="ajaxReady"></i>
                <i class="fa fa-spinner fa-pulse fa-fw" v-else></i>
            </span>
            <span class="date" v-show="fileRequest.due">{{ formattedDate }}</span>
            <span v-else class="date">Due Date</span>
            <input type="text"
                   v-model="newDate"
                   @keydown.delete.prevent="removeDate"
                   tabindex="-1"
                   ref="input"
            >
        </button>

        <div class="uneditable" v-else>
            <i class="fa fa-calendar icon"></i>
            <smart-date v-show="fileRequest.due" :date="fileRequest.due"></smart-date>
            <span v-show="! fileRequest.due">--</span>
        </div>

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
        props: ['user', 'file-request', 'index'],
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
                $(this.$refs.input).datepicker('show');
            },
            removeDate () {
                this.updateDueDate('');
                $(this.$refs.input).datepicker('hide');
            },
            updateDueDate(date) {
                var self = this;
                if (!self.ajaxReady) return;
                self.ajaxReady = false;

                self.$http.put('/fr/' + this.fileRequest.hash, {
                    due: date
                }).then((response) => {
                    // success
                    this.$emit('update-file-request', response.json(), this.index);
                    self.ajaxReady = true;
                }, (response) => {
                    // error
                    console.log('GET REQ Error!');
                    console.log(response);
                    self.ajaxReady = true;

                })
            }
        },
        mounted() {
            $(this.$refs.input).datepicker({
                dateFormat: "dd/mm/yy",
            });
        }
    }
</script>