<template>
<div class="editable-number-field">
    <span ref="placeholder" v-if="value" v-show="! editing" class="placeholder clickable" @click.stop="enterEditMode">{{ value }}</span>
    <span ref="placeholder" v-if="! value && placeholder" v-show="! editing" class="text-muted placeholder clickable" @click.stop="enterEditMode">{{ placeholder }}</span>
    <form v-show="editing" @submit.prevent="processNewValue">
        <input type="number" :step="step" v-model="newValue" ref="input" @blur="processNewValue">
    </form>
</div>
</template>
<script>
    const editableField = require("../../mixins/EditableField");
export default {
    data: function(){
        return {

        }
    },
    props: {
        step: {
            type: Number,
            default: 1
        },
    },
    methods: {

    },
    mixins: [editableField],
}
</script>