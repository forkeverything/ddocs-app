<template>
    <div class="file-date-input line-el">
        <button type="button"
                class="btn"
                :class="{'filled': date}"
                @click="pickDate"
        >
            <span v-show="date">{{ formattedDate }}</span>
            <i class="fa fa-calendar" v-show="! date"></i>
        </button>
        <input type="text"
               class="input-due line-el"
               @input.prevent.stop="onInput(event)"
               @keydown.delete.prevent="removeDate"
               placeholder="Due date"
               tabindex="-1"
               ref="input"
        >
    </div>
</template>
<script>
    export default {
        data: function() {
            return {
                date: ''
            }
        },
        props: ['value'],
        computed: {
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
                this.date = '';
                $(this.$refs.input).datepicker('hide');
            }
        },
        watch: {
            date() {
                this.$emit('input', this.date);
            }
        },
        mounted() {
            this.date = this.value;

            $(this.$refs.input).datepicker({
                onSelect: (date) => {
                    this.date = date;
                },
                dateFormat: "dd/mm/yy",
            });
        }
    }
</script>