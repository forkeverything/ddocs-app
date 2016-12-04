<template>
    <div class="recipients-editor">
        <tagger v-if="recipientEmails"
                v-model="recipientEmails"
                :validate-function="validateRecipient"
                placeholder="Recipient Emails"
        >
        </tagger>

        <ul class="list-unstyled list-inline text-right">
            <li>
                <a href="#" @click.prevent="$emit('hide')">Cancel</a>
            </li>
            <li class="pr-0">
                <a href="#" @click.prevent="save" :disabled="! canSave || ! ajaxReady">Save</a>
            </li>
        </ul>
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