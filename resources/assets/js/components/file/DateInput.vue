<template>
    <div class="file-date-input line-el">
        <button type="button"
                class="btn"
                :class="{'filled': date}"
                @click="pickDate"
        >
            <i class="fa fa-calendar" v-show="! date"></i>
            <span v-else>{{ formattedDate }}</span>
        </button>
        <input type="text"
               class="input-due line-el"
               v-model="date"
               v-datepicker
               @keydown.delete.prevent="removeDate"
               placeholder="Due date"
               tabindex="-1"
               v-el:input
        >
    </div>
</template>
<script>
    export default {
        props: ['date'],
        computed: {
            formattedDate() {
                if (!this.date) return;
                let date = moment(this.date, 'DD/MM/YYYY').format('YYYY-MM-DD');
                return Vue.filter('smartDate')(date);
            }
        },
        methods: {
            pickDate() {
                $(this.$els.input).focus();
            },
            removeDate () {
                this.date = '';
            }
        },
        ready: function () {
//        console.log(this.date);
//        console.log(Vue.filter('smartDate')('2016-09-15'));
        }
    }
</script>