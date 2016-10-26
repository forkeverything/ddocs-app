<template>
   <div class="date-range-field" @click.stop="">
   <div class="starting">
       <label>starting</label>
       <date-picker v-model="range.minValue" :filter-format="true"></date-picker>
       </div>
   <span class="dash">-</span>
   <div class="ending">
       <label>Ending</label>
       <date-picker v-model="range.maxValue" :filter-format="true"></date-picker>
       </div>
   </div>
</template>
<script>
export default {
    data: function() {
        return {
            range: ''
        }
    },
    watch: {
        range(newVal){
            this.$emit('input', newVal);
        }
    },
    props: ['value'],
    mounted() {
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