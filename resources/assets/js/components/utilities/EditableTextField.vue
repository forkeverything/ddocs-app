<template>
<div class="editable-text-field">
    <span class="placeholder clickable" v-show="! editing" @click="enterEditMode">
        {{ value }}
    </span>
    <form v-show="editing" @submit.prevent="processNewValue">
        <input type="text" v-model="newValue" v-el:input @blur="processNewValue">
    </form>
</div>
</template>
<script>
export default {
    data: function(){
        return {
            editing: false,
            newValue: ''
        }
    },
    props: ['value', 'allow-null', 'update-fn'],
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
                this.updateFn();
                this.exitEditMode();
            });
        }
    },
    ready() {
        // Create a copy of the value so we can reset it if necessary
        this.newValue = this.value;
    }
}
</script>