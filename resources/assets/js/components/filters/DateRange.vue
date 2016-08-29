<template>
   <div class="date-range-field" @click.stop="">
   <div class="starting">
       <label>starting</label>
       <input type="text" class="filter-datepicker" v-model="startDate" placeholder="date" v-datepicker>
       </div>
   <span class="dash">-</span>
   <div class="ending">
       <label>Ending</label>
       <input type="text" class="filter-datepicker" v-model="endDate" placeholder="date" v-datepicker>
       </div>
   </div>
</template>
<script>
export default {
    name: 'dateRangeField',
    computed: {
        startDate: {
            get: function() {
                if (this.min.replace(/\s/g, "").length > 0) {
                    return moment(this.min, "YYYY-MM-DD").format('DD/MM/YYYY');
                }
            },
            set: function(newVal) {
                if(newVal.replace(/\s/g, "").length > 0) {
                    this.min = moment(newVal, "DD/MM/YYYY").format("YYYY-MM-DD");
                }
            }
        },
        endDate: {
            get: function() {
                if (this.max.replace(/\s/g, "").length > 0) {
                    return moment(this.max, "YYYY-MM-DD").format('DD/MM/YYYY');
                }
            },
            set: function(newVal) {
                if(newVal.replace(/\s/g, "").length > 0) {
                    this.max = moment(newVal, "DD/MM/YYYY").format("YYYY-MM-DD");
                }
            }
        }
    },
    props: ['min', 'max']
}
</script>