<template>
    <div class="datepicker"
         :class="{
        'button-only': buttonOnly
     }"
    >
        <button type="button"
                class="btn"
                :class="{'filled': date}"
                @click="pickDate"
                @keydown.delete="removeDate($event)"
        >
            <i class="fa fa-calendar" v-show="! hideIcon && (! buttonOnly || keepButton || ! date)"></i>
            <smart-date v-show="buttonOnly && date" :date="date"></smart-date>
            <span class="placeholder" v-show="buttonOnly && placeholder && ! date">{{ placeholder }}</span>
        </button>
        <input type="text"
               @keydown.delete="removeDate($event)"
               placeholder="Due date"
               tabindex="-1"
               ref="input"
               v-model="date"
        >
    </div>
</template>
<script>
    export default {
        data: function(){
            return {
                date: ''
            }
        },
        props: ['value', 'formatted', 'placeholder', 'button-only', 'keep-button', 'carbon', 'filter-format', 'hide-icon'],
        computed: {
            formattedDate() {
                if(! this.date) return null;
                if(this.carbon) return moment(this.date, ["DD/MM/YYYY", "YYYY-MM-DD HH:mm:ss"]).format("YYYY-MM-DD HH:mm:ss");
                if(this.filterFormat) return moment(this.date, ["DD/MM/YYYY", "YYYY-MM-DD HH:mm:ss"]).format("YYYY-MM-DD");
                return this.date;
            }
        },
        methods: {
            pickDate() {
                $(this.$refs.input).datepicker('show');
            },
            removeDate (event) {
                if(this.buttonOnly) {
                    event.preventDefault();
                    this.date = '';
                    this.$emit('on-change', this.formattedDate);
                    $(this.$refs.input).datepicker('hide');
                }
            }
        },
        watch: {
            value(newVal) {
                this.date = newVal;
            },
            date() {
                this.$emit('input', this.formattedDate);
            }
        },
        mounted(){
            this.date = this.value;

            $(this.$refs.input).datepicker({
                onSelect: (date) => {
                    this.date = date;
            this.$emit('on-change', this.formattedDate);
                },
                dateFormat: "dd/mm/yy"
            });
        }
    }
</script>