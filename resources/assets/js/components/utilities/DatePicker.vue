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
    >
        <smart-date v-show="buttonOnly && date" :date="date"></smart-date>
        <i class="fa fa-calendar" v-show="! buttonOnly || ! date"></i>
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
    props: ['value', 'formatted', 'placeholder', 'button-only'],
    computed: {},
    methods: {
        pickDate() {
            $(this.$refs.input).datepicker('show');
        },
        removeDate (event) {
            if(this.buttonOnly) {
                event.preventDefault();
                this.date = '';
                $(this.$refs.input).datepicker('hide');
            }
        }
    },
    watch: {
        value(newVal) {
          this.date = newVal;
        },
        date() {
            this.$emit('input', this.date);
        }
    },
    mounted(){
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