<template>
    <div id="checklist-make" class="container">
        <div class="maker">

            <h3>New Checklist</h3>

            <form-errors></form-errors>


            <form id="form-checklist-make" action="/c/make" method="POST">
                <div class="inline-label">
                    <label class="text-muted">To: </label>
                    <tagger v-model="checklistRecipients"
                            :validate-function="validateRecipient"
                            :placeholder="'Emails'"
                            @updated-tags="setRecipients"
                    >
                    </tagger>
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
                </ul>
                <files-selecter v-model="files"></files-selecter>
            </form>
            <div class="text-right">
                <button type="button" id="btn-create-list" class="btn btn-primary" @click="sendChecklist" :disabled="
                    ! canSendChecklist">{{ submitButtonText }}
                </button>
            </div>
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
        computed: {
            checklistNameText: function () {
                // Used to display a default when a name isn't given
                if (this.checklistName) return this.checklistName;
                return 'List Name';
            },
            validFiles: function () {
                // Only deal with files that have names
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
            setRecipients(recipients) {
                this.checklistRecipients = recipients;
            },
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

                vueClearValidationErrors();
                if (!this.ajaxReady) return;
                this.ajaxReady = false;


                this.$http.post('/api/checklists', {
                    recipients: this.checklistRecipients,
                    name: this.checklistName,
                    description: this.checklistDescription,
                    requested_files: this.validFiles
                }).then((response) => {
                    router.push('/c/' + response.data);
                    location.href = "/c/" + response.data;
                }, (response) => {
                    console.log(response);
                    vueValidation(response);
                    this.ajaxReady = true;
                });
            }
        }
    };
</script>