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
               v-model="datePicker"
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
            datePicker: {
                get() {
                    return this.date;
                },
                set(newDate) {
                    newDate = newDate || null;
                    if(newDate !== this.date) vueGlobalEventBus.$emit('updated-date', newDate);
                    this.date = newDate;
                }
            },
            formattedDate() {
                if (!this.date) return;
                return Vue.filter('smartDate')(this.date);
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
        watch: {},
        ready: function () {}
    }
</script>