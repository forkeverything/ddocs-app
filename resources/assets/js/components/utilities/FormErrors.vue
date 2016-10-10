<template>
<div class="validation-errors" v-show="! stealth && errorList.length > 0">
    <ul class="errors-list list-unstyled">
        <li v-for="error in errorList">{{ error }}</li>
    </ul>
</div>
</template>
<script>
export default {
    data: function(){
        return {
            errors: {}
        }
    },
    computed: {
      errorList() {
          let errorList = [];
          _.forIn(this.errors, (value, key) => {
              // We only care about the first error for each field if there are multiple
              // errors present.
              errorList.push(value[0]);
          });
          return errorList;
      }
    },
    props: ['event-bus', 'stealth'],
    methods: {
      registerEvents: function(eventBus = vueGlobalEventBus) {

          eventBus.$on('new-errors', (errors) => {
              this.errors = errors;
              this.$emit('got-error', errors);     // propagate our errors up in case our parent component wants to know.
          });



          eventBus.$on('clear-errors', (errors) => this.errors = {});
      }
    },
    created(){
        this.registerEvents(this.eventBus);
    }
}
</script>