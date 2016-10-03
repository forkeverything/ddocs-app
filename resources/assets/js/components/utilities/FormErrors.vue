<template>
<div class="validation-errors" v-show="errors.length > 0">
    <ul class="errors-list list-unstyled" v-show="errors.length > 0">
    <li v-for="error in errors">{{ error }}</li>
    </ul>
</div>
</template>
<script>
export default {
    data: function(){
        return {
            errors: []
        }
    },
    props: ['event-bus'],
    methods: {
      registerEvents: function(eventBus = vueGlobalEventBus) {

          eventBus.$on('new-errors', (errors) => {
              let newErrors = [];
              _.forIn(errors, (value, key) => {
                  // We only care about the first error for each field if there are multiple
                  // errors present.
                  newErrors.push(value[0]);
              });
              this.errors = newErrors;
          });

          eventBus.$on('clear-errors', (errors) => this.errors = []);
      }
    },
    created(){
        this.registerEvents(this.eventBus);
    },
    beforeDestroy(){
        this.eventBus.$off('new-errors');
        this.eventBus.$off('clear-errors');
    }
}
</script>