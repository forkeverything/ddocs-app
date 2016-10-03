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
               @keydown.delete.prevent="removeDate"
               placeholder="Due date"
               tabindex="-1"
               ref="input"
        >
    </div>
</template>
<script>
    export default {
        props: ['value'],
        computed: {
            datePicker: {
                get() {
                    return this.date;
                },
                set(newDate) {
                    newDate = newDate || null;
                    this.$emit('input', newDate);
                }
            },
            formattedDate() {
                if (!this.date) return;
                return Vue.filter('smartDate')(this.date);
            }
        },
        methods: {
            pickDate() {
                $(this.$refs.input).datepicker('show');
            },
            removeDate () {
                this.datePicker = '';
                $(this.$refs.input).datepicker('hide');
            }
        },
        watch: {},
        mounted() {
            $(this.$refs.input).datepicker({
                dateFormat: "dd/mm/yy",
            });
        }
    }
</script>