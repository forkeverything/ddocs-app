Vue.component('form-errors', {
    template: '<div class="validation-errors" v-show="errors.length > 0">' +
    '<ul class="errors-list list-unstyled"' +
    'v-show="errors.length > 0"' +
    '>' +
    '<li v-for="error in errors">{{ error }}</li>' +
    '</ul>' +
    '</div>',
    data: function () {
        return {
            errors: []
        }
    },
    ready: function() {
        var self = this;

        vueEventBus.$on('new-errors', function (errors) {
            var newErrors = [];
            _.forEach(errors, function (error) {
                if (newErrors.indexOf(error[0]) == -1) newErrors.push(error[0]);
            });
            self.errors = newErrors;
        });

        vueEventBus.$on('clear-errors', function(errors) {
            self.errors = [];
        });
    }
});