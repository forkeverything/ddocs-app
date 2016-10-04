<template>
    <div class="integer-range-field">
    <input type="number" class="form-control" min="0" @input="onInput('minValue', $event)">
    <span class="dash">-</span>
    <input type="number" class="form-control" min="0" @input="onInput('minValue', $event)">
    </div>
</template>
<script>
export default {
    data: function() {
        return {
            range: ''
        }
    },
    props: ['value'],
    methods: {
        onInput(field, event){
            this.range[field] = event.target.value;
            this.$emit('input', this.range);
        }
    },
    mounted(){
        this.range = this.value;
        if(! this.range) {
            // if value hasn't been initialized
            this.range = {
                minValue: '',
                maxValue: ''
            }
        }
    }
}
</script>