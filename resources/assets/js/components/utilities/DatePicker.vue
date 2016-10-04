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
        <span v-show="buttonOnly && date">{{ formattedDate }}</span>
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
    computed: {
        formattedDate() {
            if (!this.date) return;
            if(this.formatted) return Vue.filter('smartDate')(this.date);
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
                $(this.$refs.input).datepicker('hide');
            }
        }
    },
    watch: {
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