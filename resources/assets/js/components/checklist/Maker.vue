<template>
    <div>
        <form-errors></form-errors>
        <form id="form-checklist-make" action="/c/make" method="POST">
            <div class="inline-label">
                <label class="text-muted">To: </label>
                <tagger :tags.sync="checklistRecipients" :validate-function="validateRecipient" :placeholder="'Emails'"></tagger>
            </div>
            <h2 id="title-checklist-name"
                v-show="! editingName"
                @click="toggleEditingName"
                @focus="toggleEditingName"
                :class="{
                                    'filled': checklistName
                                }"
                tabindex="0"
            >{{ checklistNameText }}</h2>
            <input id="input-checklist-name" type="text" class="form-control" v-show="editingName"
                   @blur="toggleEditingName" v-model="checklistName" name="name">
            <textarea id="textarea-new-checklist-description" rows="2" class="autosize form-control borderless"
                      placeholder="description" v-model="checklistDescription" name="description"></textarea>
            <hr>
            <h4>Files List ({{ fileCount }})</h4>
            <maker-files :files.sync="files"></maker-files>
        </form>
        <div class="text-right">
            <button type="button" class="btn btn-primary" @click="sendChecklist" :disabled="
                    ! canSendChecklist">{{ submitButtonText }}
            </button>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                ajaxReady: true,
                checklistRecipients: [],
                checklistName: '',
                checklistDescription: '',
                editingName: false,
                files: [
                    {
                        name: '',
                        description: '',
                        due: ''
                    }
                ]
            }
        },
        props: ['user'],
        computed: {
            checklistNameText: function () {
                // Used to display a default when a name isn't given
                if (this.checklistName) return this.checklistName;
                return 'List Name';
            },
            validFiles: function () {
                // Only return files with names
                return _.filter(this.files, function (file) {
                    return file.name;
                });
            },
            fileCount: function () {
                return this.validFiles.length;
            },
            canSendChecklist: function () {
                // Required fields...
                return this.checklistRecipients.length > 0 && this.checklistName && this.fileCount > 0;
            },
            submitButtonText: function () {
                if (!this.ajaxReady) return 'Saving...';
                return 'Create List';
            }
        },
        methods: {
            validateRecipient: function(tagger, recipient) {
                if(! validateEmail(recipient))  {
                    tagger.validateError = 'Please enter a valid email.';
                    tagger.showError = true;
                    setTimeout(() => {
                        tagger.showError = false;
                    }, 1500);
                    return false;
                }
                return true;

            },
            toggleEditingName: function () {
                this.editingName = !this.editingName;
                if (this.editingName) {
                    this.$nextTick(function () {
                        $('#input-checklist-name').focus();
                    });
                }
            },
            sendChecklist: function () {

                if (!this.user) {
                    vueGlobalEventBus.$emit('show-registration-modal');
                    return;
                }

                var self = this;
                vueClearValidationErrors();
                if (!self.ajaxReady) return;
                self.ajaxReady = false;


                self.$http.post('/c/make', {
                    recipients: self.checklistRecipients,
                    name: self.checklistName,
                    description: self.checklistDescription,
                    requested_files: self.validFiles
                }).then((response) => {
                    location.href = "/c/" + response.data;
                }, (response) => {
                    console.log(response);
                    vueValidation(response);
                    self.ajaxReady = true;
                });
            }
        },
        ready: function () {
            vueGlobalEventBus.$on('created-account', function (user) {
                this.user = user;
                this.sendChecklist();
            }.bind(this));
        }
    };
</script>