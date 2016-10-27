module.exports = {
    data: function(){
        return {
            editing: false,
            newValue: ''
        }
    },
    props: ['value', 'allow-null', 'placeholder'],
    watch: {
        value(newVal) {
            if(this.newValue !== newVal) this.newValue = newVal;
        }
    },
    methods: {
        enterEditMode(){
            this.editing = true;
            this.$nextTick(() => {
                $(this.$refs.input).focus();
            });
        },
        exitEditMode(){
            this.editing = false;
            this.newValue = this.value;
        },
        processNewValue() {
            if(! this.allowNull && ! this.newValue || (this.newValue === this.value) ) return this.exitEditMode();
            this.$emit('input', this.newValue); // v-model 2 way binding
            // In case we want to manually update or persist changes without
            // using a watcher on the bound data.
            this.$emit('on-change', this.newValue);
            this.$nextTick(this.exitEditMode);
        }
    },
    mounted() {
        // Create a copy of the value so we can reset it if necessary
        this.newValue = this.value;
    }
};