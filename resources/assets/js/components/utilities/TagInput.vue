<template>
    <div class="tag-input">
            <pre ref="sizer"
                 class="sizer">{{ value }}</pre>
        <input ref="input"
               :value="value"
               :style="{ width: inputWidth }"
               @keyup.enter.prevent.stop="addTag"
               @keydown.delete="deleteInput"
               @keydown.left="leftInput"
               @keydown.right="rightInput"
               @focus="togglePlaceholder"
               @blur="togglePlaceholder"
               @input="onInput"
        >
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                inputWidth: '20px'
            }
        },
        props: ['value', 'add-tag', 'remove-tag', 'input-position', 'show-placeholder', 'focus-tag'],
        watch: {
            value() {
                this.$nextTick(() => {
                    // 20px runway for calculating width time, needed to kill the flicker.
                    this.inputWidth = Math.round($(this.$refs.sizer).width()) + 20 + 'px';
                });
            }
        },
        methods: {
            onInput(event) {
                this.$emit('input', event.target.value);
            },
            togglePlaceholder() {
                this.$emit('toggle-placeholder');
            },
            deleteInput: function () {
                // If at the end or just backspacing
                if (!this.inputPosition || this.value) return;
                this.removeTag(this.inputPosition - 1);
            },
            leftInput: function () {
                if (!this.$refs.input.selectionEnd) {
                    this.focusTag(this.inputPosition - 1);
                }
            },
            rightInput: function () {
                if (this.value.length === this.$refs.input.selectionEnd) this.focusTag(this.inputPosition);
            }
        }
    }
</script>