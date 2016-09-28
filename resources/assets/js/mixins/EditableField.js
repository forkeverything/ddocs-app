module.exports = {
    data: function(){
        return {
            editing: false,
            newValue: ''
        }
    },
    props: ['value', 'allow-null', 'update-fn', 'placeholder'],
    watch: {
        value(newVal) {
            if(this.newValue !== newVal) this.newValue = newVal;
        }
    },
    methods: {
        enterEditMode(){
            this.editing = true;
            this.$nextTick(() => {
                $(this.$els.input).focus();
            });
        },
        exitEditMode(){
            this.editing = false;
            this.newValue = this.value;
        },
        processNewValue() {
            if(! this.allowNull && ! this.newValue || (this.newValue === this.value) ) return this.exitEditMode();
            this.value = this.newValue;
            this.$nextTick(() => {
                if(this.updateFn) this.updateFn();
                this.exitEditMode();
            });
        }
    },
    ready() {
        // Create a copy of the value so we can reset it if necessary
        this.newValue = this.value;
    }
};