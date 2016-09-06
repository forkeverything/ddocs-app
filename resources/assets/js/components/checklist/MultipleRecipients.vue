<template>
    <div id="recipients-selector">
        <div id="recipients-wrap" @click="focusRecipientInput">
                    <span class="placeholder"
                          v-show="checklistRecipients.length < 1 && ! newRecipient && showRecipientPlaceholder"
                    >
                        Email
                    </span>
            <input v-if="checklistRecipients.length > 0 && recipientInputPosition === 0"
                   id="input-recipient-email"
                   class="form-control"
                   v-model="newRecipient"
                   :style="{ width: recipientInputWidth }"
                   @keyup.delete.prevent.stop="removeRecipient(index - 1)"
                   @keyup.enter.prevent.stop="addRecipient"
                   @keyup.left.prevent.stop="focusRecipient(index)"
                   @keyup.right.prevent.stop="focusRecipient(index + 1)"
            >
            <template v-for="(index, recipient) in checklistRecipients"
                      track-by="$index"
                      v-if="checklistRecipients.length > 0"
            >
                <button type="button"
                        class="btn single-recipient"
                        @click.stop=""
                        @keyup.left.prevent.stop="focusRecipient(index - 1)"
                        @keyup.delete.prevent.stop="removeRecipient(index)"
                        @keyup.right.prevent.stop="focusRecipient(index + 1)"
                >
                    {{ recipient }}
                </button>
                <input v-if="recipientInputPosition === (index + 1)"
                       id="input-recipient-email"
                       class="form-control"
                       v-model="newRecipient"
                       :style="{ width: recipientInputWidth }"
                       @keyup.delete.prevent.stop="removeRecipient(index)"
                       @keyup.enter.prevent.stop="addRecipient"
                       @keyup.left.prevent.stop="focusRecipient(index)"
                       @keyup.right.prevent.stop="focusRecipient(index + 1)"
                >
            </template>
            <input v-if="checklistRecipients.length < 1" id="input-recipient-email"
                   class="form-control" v-model="newRecipient"
                   @keyup.enter="addRecipient" :style="{ width: recipientInputWidth }"
                   @focus="toggleRecipientPlaceholder" @blur="toggleRecipientPlaceholder">
            <span class="input-sizer">{{ newRecipient }}</span>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                recipientInputPosition: 0,
                showRecipientPlaceholder: true,
                newRecipient: '',
                recipientInputWidth: '4px'
            }
        },
        props: ['checklist-recipients'],
        computed: {},
        methods: {
            focusRecipient: function (index) {
                let el = $('.single-recipient')[index];
                if (el) {
                    $(el).focus();
                } else if (!$('.single-recipient')[index + 1]) {
                    this.recipientInputPosition = index;
                    // at end of line, so focus on input
                    this.$nextTick(() => $('#input-recipient-email').focus());
                }
            },
            toggleRecipientPlaceholder: function () {
                this.showRecipientPlaceholder = !this.showRecipientPlaceholder;
            },
            focusRecipientInput: function () {
                $('#input-recipient-email').focus();
            },
            addRecipient: function () {
                this.checklistRecipients.push(this.newRecipient);
                this.newRecipient = '';
                this.recipientInputPosition++;
                this.$nextTick(() => $('#input-recipient-email').focus());
            },
            removeRecipient: function (index) {
                // If we're at the end or actually just backspacing - return
                if (! this.recipientInputPosition || this.newRecipient ) return;
                this.checklistRecipients.splice(index, 1);
                this.recipientInputPosition = index;
                this.$nextTick(() => $('#input-recipient-email').focus());
            },
        },
        ready: function () {
            var self = this;
            this.$watch('newRecipient', () => {
                this.$nextTick(() => {
                    self.recipientInputWidth = $('.input-sizer').width() + 'px';
                });
            });
        }
    }
</script>