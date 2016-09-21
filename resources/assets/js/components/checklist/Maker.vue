<template>
    <div class="maker">

            <h3>New Checklist</h3>

        <form-errors></form-errors>

        <div class="weighting-switch">
            <label>Weightings</label>
            <toggle-switch :model.sync="weightings"></toggle-switch>
        </div>
        <form id="form-checklist-make" action="/c/make" method="POST">
            <div class="inline-label">
                <label class="text-muted">To: </label>
                <tagger :tags.sync="checklistRecipients" :validate-function="validateRecipient"
                        :placeholder="'Emails'"></tagger>
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
            <h4 class="title-files">Files</h4>
            <ul class="files-summary text-muted list-unstyled list-inline">
                <li class="num_files">Count {{ fileCount }}</li>
                <li class="weightings" v-show="weightings">Total Weightings {{ totalWeights }}%</li>
            </ul>
            <maker-files :files.sync="files" :weightings="weightings"></maker-files>
        </form>
        <div class="text-right">
            <button type="button" id="btn-create-list" class="btn btn-primary" @click="sendChecklist" :disabled="
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
                        due: '',
                        weighting: ''
                    }
                ],
                weightings: true
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
                var self = this;
                // Only deal with files that have names
                let filesWithNames = _.filter(this.files, function (file) {
                    return file.name;
                });
                // Map through and include weightings based on our settings
                return _.map(filesWithNames, (file) => {
                    if (!self.weightings) file.weighting = '';
                    return file
                });
            },
            fileCount: function () {
                return this.validFiles.length;
            },
            totalWeights: function () {
                let totalWeightings = 0;
                _.map(this.files, (file) => {
                    if (file.weighting) totalWeightings += parseFloat(file.weighting);
                });
                return totalWeightings.toFixed(2);
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
            validateRecipient: function (tagger, recipient) {
                if (!validateEmail(recipient)) {
                    tagger.validateError = 'Please enter a valid email.';
                    tagger.showError = true;
                    setTimeout(function () {
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