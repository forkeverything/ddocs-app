<template>
    <div class="tagger">
        <div class="tagger-container"
             @click="focusInput"
             v-el:container
             :class="{'with-error': showError }"
        >
            <span class="placeholder"
                  v-show="emptyContainer && ! newTag && showPlaceholder"
            >
                {{ placeholder }}
            </span>
            <div class="tag-input-container" v-if="inputPosition === 0">
                <tag-input :add-tag="addTag"
                           :remove-tag="removeTag"
                           :focus-tag="focusTag"
                           :new-tag.sync="newTag"
                           :input-position.sync="inputPosition"
                           :show-placeholder.sync="showPlaceholder"
                >
                </tag-input>
            </div>
            <template v-for="(index, tag) in tags"
                      track-by="$index"
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
                    <tag-input :add-tag="addTag"
                               :remove-tag="removeTag"
                               :focus-tag="focusTag"
                               :new-tag.sync="newTag"
                               :input-position.sync="inputPosition"
                               :show-placeholder.sync="showPlaceholder"
                    >
                    </tag-input>
                </div>
            </template>
        </div>
        <div class="tagger-error-container animated" v-show="showError" transition="fade">
            {{ validateError }}
        </div>
    </div>
</template>
<script>
    // Input Component
    let TagInput = Vue.extend({
        template: `
        <div class="tag-input">
            <pre v-el:sizer
                    class="sizer">{{ newTag }}</pre>
            <input  v-el:input
                    v-model="newTag"
                    :style="{ width: inputWidth }"
                    @keyup.enter.prevent.stop="addTag"
                    @keydown.delete="deleteInput"
                    @keydown.left="leftInput"
                    @keydown.right="rightInput"
                    @focus="togglePlaceholder"
                    @blur="togglePlaceholder"
            >
        </div>
    `,
        data: function () {
            return {
                inputWidth: '0px'
            }
        },
        methods: {
            togglePlaceholder: function () {
                this.showPlaceholder = !this.showPlaceholder;
            },
            deleteInput: function () {
                // If at the end or just backspacing
                if (!this.inputPosition || this.newTag) return;
                this.removeTag(this.inputPosition - 1);
            },
            leftInput: function () {
                if (!this.$els.input.selectionEnd) {
                    this.focusTag(this.inputPosition - 1);
                }
            },
            rightInput: function () {
                if(this.newTag.length === this.$els.input.selectionEnd) this.focusTag(this.inputPosition);
            }
        },
        props: ['new-tag', 'add-tag', 'remove-tag', 'input-position', 'show-placeholder', 'focus-tag'],
        ready: function () {
            this.$watch('newTag', () => {
                this.$nextTick(() => {
                    // 1px for rounding
                    this.inputWidth = $(this.$els.sizer).width() + 2 + 'px';
                });
            });
        }
    });

    // Main Tagger Component
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
                return this.tags.length < 1;
            }
        },
        props: ['tags', 'placeholder', 'validate-function'],
        methods: {
            focusInput: function () {
                $(this.$els.container).find('.tag-input input').focus();
            },
            addTag: function () {

                if(this.validateFunction && ! this.validateFunction(this, this.newTag)) {
                    return;
                }

                // No empty tag with spaces
                if (!this.newTag.trim()) return;
                // insert it to where the input is
                this.tags.splice(this.inputPosition, 0, this.newTag);
                // clear input
                this.newTag = '';
                // move input up 1
                this.inputPosition++;
                // re-focus
                this.$nextTick(this.focusInput);
            },
            removeTag: function (index) {
                // delete tag at index
                this.tags.splice(index, 1);
                // move input position up to where removed tag was
                this.inputPosition = index;
                // re-focus
                this.$nextTick(this.focusInput);
            },
            focusTag: function (index) {
                let el = $(this.$els.container).find('.single-tag')[index];
                if (el) {
                    $(el).focus();
                } else if (index === this.tags.length) {
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
                if(this.inputPosition === index + 1) {
                    this.focusInput();
                } else {
                    this.focusTag(index + 1);
                }
            }
        },
        components: {
            TagInput
        }
    }
</script>