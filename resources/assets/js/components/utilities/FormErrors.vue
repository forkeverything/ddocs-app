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

          var self = this;
          eventBus.$on('new-errors', function (errors) {
              var newErrors = [];
              _.forIn(errors, (value, key) => {
                  // We only care about the first error for each field if there are multiple
                  // errors present.
                  newErrors.push(value[0]);
              });
              self.errors = newErrors;
          });

          eventBus.$on('clear-errors', function(errors) {
              self.errors = [];
          });
      }
    },
    ready: function() {
        this.registerEvents(this.eventBus);
    }
}
</script>