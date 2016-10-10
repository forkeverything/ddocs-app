module.exports = {
    data() {
        return {
            formErrors: ''
        }
    },
    methods: {
        gotError(errors) {
            this.formErrors = errors;
        }
    }
};