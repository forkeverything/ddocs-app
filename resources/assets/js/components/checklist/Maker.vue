<template>
    <div id="checklist-make" class="main-content">
        <div class="row">
            <div class="password">
                <i class="fa fa-lock"
                   :class="{
                        'active': password
                   }"
                   data-toggle="tooltip"
                   data-placement="bottom"
                   title="Password required for guests to view checklist."
                ></i>
                <input id="field-password"
                       type="text"
                       class="form-control"
                       v-model="password"
                       placeholder="Password"
                >
            </div>
        </div>
        <form-errors></form-errors>
        <form id="form-checklist-make" action="/c/make" method="POST">
            <div class="inline-label">
                <label class="text-muted">To: </label>
                <tagger v-model="checklistRecipients"
                        :validate-function="validateRecipient"
                        placeholder="Emails"
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
            <h4 class="title-files">Files</h4>
            <ul class="files-summary text-muted list-unstyled list-inline">
                <li class="num_files">Count {{ fileCount }}</li>
            </ul>
            <files-selecter v-model="files"></files-selecter>
        </form>
        <div class="btn-submit-wrap text-right">
            <button type="button" id="btn-create-list" class="btn btn-success" @click="sendChecklist" :disabled="
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
                ],
                password: ''
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
            validateRecipient: function (tagger, recipient) {
                if (!validateEmail(recipient)) return tagger.displayError('Please enter a valid email.');
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
                    requested_files: this.validFiles,
                    password: this.password
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    router.push('/c/' + response.data);
                    location.href = "/c/" + response.data;
                }, (response) => {
                    console.log(response);
                    vueValidation(response);
                    this.ajaxReady = true;
                });
            },
            togglePasswordField(){
                this.passwordField = !this.passwordField;
            }
        },
        mounted() {
            this.$store.commit('setTitle', 'Checklist: Make');
        }
    };
</script>