<template>
    <div class="editable-text-area">
    <span class="placeholder clickable" v-show="! editing" @click.stop="enterEditMode">
        <span v-if="value">{{ value }}</span><span v-if="! value && placeholder" class="text-muted">{{ placeholder }}</span>
    </span>
        <form v-show="editing" @submit.prevent="processNewValue">
            <textarea v-model="newValue" ref="input" @blur="processNewValue"></textarea>
        </form>
    </div>
</template>
<script>
    const editableField = require("../../mixins/EditableField");
    export default {
        mixins: [editableField],
        mounted() {
            this.$nextTick(() => {
                autosize($(this.$refs.input));
            });
        }
    };
</script>