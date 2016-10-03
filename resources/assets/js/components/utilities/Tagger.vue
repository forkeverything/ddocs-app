<template>
    <div class="tagger">
        <div class="tagger-container"
             @click="focusInput"
             ref="container"
             :class="{'with-error': showError }"
        >
            <span class="placeholder"
                  v-show="emptyContainer && ! newTag && showPlaceholder"
            >
                {{ placeholder }}
            </span>
            <div class="tag-input-container" v-if="inputPosition === 0">
                <tag-input v-model="newTag"
                           :add-tag="addTag"
                           :remove-tag="removeTag"
                           :focus-tag="focusTag"
                           :input-position="inputPosition"
                           @toggle-placeholder="togglePlaceholder"
                >
                </tag-input>
            </div>
            <template v-for="(tag, index) in tags"
                      :key="index"
                      v-if="! emptyContainer"
            >
                <button type="button"
                        class="single-tag btn btn-tag"
                        @click.stop=""
                        @keydown.left.prevent.stop="leftTag(index)"
                        @keydown.delete.prevent.stop="removeTag(index)"
                        @keydown.right.prevent.stop="rightTag(index)"
                >
                    {{ tag }}
                </button>
                <div class="tag-input-container" v-if="inputPosition === (index + 1)">
                    <tag-input v-model="newTag"
                               :add-tag="addTag"
                               :remove-tag="removeTag"
                               :focus-tag="focusTag"
                               :input-position="inputPosition"
                               @toggle-placeholder="togglePlaceholder"
                    >
                    </tag-input>
                </div>
            </template>
        </div>
        <div class="tagger-error-container" v-show="showError">
            {{ validateError }}
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                newTag: '',
                showPlaceholder: true,
                inputPosition: 0,
                showError: false,
                validateError: ''
            }
        },
        computed: {
            emptyContainer: function () {
                return this.value.length < 1;
            }
        },
        props: ['value', 'placeholder', 'validate-function'],
        methods: {
            togglePlaceholder() {
                this.showPlaceholder = !this.showPlaceholder;
            },
            focusInput() {
                $(this.$refs.container).find('.tag-input input').focus();
            },
            addTag: function () {

                if (this.validateFunction && !this.validateFunction(this, this.newTag)) {
                    return;
                }

                // No empty tag with spaces
                if (!this.newTag.trim()) return;
                // insert it to where the input is
                this.value.splice(this.inputPosition, 0, this.newTag);
                // clear input
                this.newTag = '';
                // move input up 1
                this.inputPosition++;
                // re-focus
                this.$nextTick(this.focusInput);
            },
            removeTag: function (index) {
                // delete tag at index
                this.value.splice(index, 1);
                // move input position up to where removed tag was
                this.inputPosition = index;
                // re-focus
                this.$nextTick(this.focusInput);
            },
            focusTag: function (index) {
                let el = $(this.$refs.container).find('.single-tag')[index];
                if (el) {
                    $(el).focus();
                } else if (index === this.value.length) {
                    // at end of tags - focus on input
                    this.inputPosition = index;
                    // at end of line, so focus on input
                    this.$nextTick(this.focusInput);
                }
            },
            leftTag: function (index) {
                if (this.inputPosition === index) {
                    this.focusInput();
                } else {
                    this.focusTag(index - 1);
                }
            },
            rightTag: function (index) {
                if (this.inputPosition === index + 1) {
                    this.focusInput();
                } else {
                    this.focusTag(index + 1);
                }
            }
        }
    }
</script>