<template>
    <div class="recipients-editor">
        <tagger v-if="recipientEmails"
                v-model="recipientEmails"
                :validate-function="validateRecipient"
                placeholder="Recipient Emails"
        >
        </tagger>
        <div class="text-right buttons">
            <button type="button" class="btn btn-default btn-sm" @click="$emit('hide')">Cancel</button>
            <button type="button" class="btn btn-info btn-sm" :disabled="! canSave || ! ajaxReady" @click="save">Save
            </button>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                ajaxReady: true,
                recipientEmails: ''
            }
        },
        props: ['recipients'],
        computed: {
            checklist() {
                return this.$store.state.checklist.data;
            },
            canSave() {
                return this.recipientEmails.length > 0;
            }
        },
        methods: {
            validateRecipient: function (tagger, recipient) {
                if (!validateEmail(recipient)) return tagger.displayError('Please enter a valid email.');
                if (_.indexOf(this.recipientEmails, recipient) !== -1) return tagger.displayError('Email already added.');
                return true;
            },
            save() {
                if (!this.ajaxReady) return;
                this.ajaxReady = false;
                this.$http.put(`/api/c/${ this.checklist.hash }/recipients`, {
                    recipients: this.recipientEmails
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    let newRecipients = response.json();
                    this.$store.commit('checklist/UPDATE_RECIPIENTS', newRecipients);
                    this.$emit('hide');
                    this.$nextTick(() => this.ajaxReady = true);
                }, (response) => {
                    // error
                    console.log("error posting to: `/c/recipients`");
                    this.ajaxReady = true;
                });
            }
        },
        mounted() {
            this.recipientEmails = _.map(this.checklist.recipients, (recipient) => {
                return recipient.email;
            });
        }
    }
</script>