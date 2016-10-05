<template>
    <div id="selected-file-date">

        <date-picker v-if="isOwner" v-model="date" :formatted="true" :placeholder="'Due date'"></date-picker>


        <div class="uneditable" v-if="! isOwner">
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
                date: ''
            }
        },
        props: ['is-owner', 'file-request', 'index'],
        computed: {},
        watch: {
            date() {
                if(this.date !== this.fileRequest.due) this.updateDueDate();
            }
        },
        methods: {
            updateDueDate() {
                if (!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.put('/fr/' + this.fileRequest.hash, {
                    due: this.date
                }).then((response) => {
                    // success
                    this.$emit('update-file-request', response.json(), this.index);
                    this.ajaxReady = true;
                }, (response) => {
                    // error
                    console.log('GET REQ Error!');
                    console.log(response);
                    this.ajaxReady = true;

                })
            }
        },
        mounted() {
            this.date = this.fileRequest.due;
        }
    }
</script>