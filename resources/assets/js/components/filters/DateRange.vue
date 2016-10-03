<template>
   <div class="date-range-field" @click.stop="">
   <div class="starting">
       <label>starting</label>
       <input type="text" class="filter-datepicker" v-model="startDate" placeholder="date" ref="min-date">
       </div>
   <span class="dash">-</span>
   <div class="ending">
       <label>Ending</label>
       <input type="text" class="filter-datepicker" v-model="endDate" placeholder="date" ref="max-date">
       </div>
   </div>
</template>
<script>
export default {
    name: 'dateRangeField',
    computed: {
        startDate: {
            get: function() {
                if (this.value.minValue.replace(/\s/g, "").length > 0) {
                    return moment(this.value.minValue, "YYYY-MM-DD").format('DD/MM/YYYY');
                }
            },
            set: function(newVal) {
                if(newVal.replace(/\s/g, "").length > 0) {
                    this.value.minValue = moment(newVal, "DD/MM/YYYY").format("YYYY-MM-DD");
                }
            }
        },
        endDate: {
            get: function() {
                if (this.value.maxValue.replace(/\s/g, "").length > 0) {
                    return moment(this.value.maxValue, "YYYY-MM-DD").format('DD/MM/YYYY');
                }
            },
            set: function(newVal) {
                if(newVal.replace(/\s/g, "").length > 0) {
                    this.value.maxValue = moment(newVal, "DD/MM/YYYY").format("YYYY-MM-DD");
                }
            }
        }
    },
    props: ['value'],
    mounted() {
        $(this.$refs.minDate).datepicker({
            dateFormat: "dd/mm/yy",
        });
        $(this.$refs.maxDate).datepicker({
            dateFormat: "dd/mm/yy",
        });
    }
}
</script>