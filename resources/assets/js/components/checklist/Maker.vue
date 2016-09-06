<template>
    <div>
        <form-errors></form-errors>
        <form id="form-checklist-make" action="/checklist/make" method="POST">
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
            <p class="text-muted">Hit Enter/Return to insert a new file. Delete files by clearing it's name.</p>
            <ul class="list-files list-unstyled">
                <li v-for="(index, file) in files" class="single-file">

                    <span class="icon-file line-el">
                    <i class="fa fa-file"></i>
                    </span>


                    <input type="text"
                           class="form-control input-file-name line-el"
                           v-model="file.name"
                           placeholder="File name"
                           @keyup.enter="addAnotherFileAfter(file)"
                           @keyup.delete="removeFile(file)"
                           @keyup.up="goTo('prev', file)"
                           @keyup.down="goTo('next', file)"
                           :name="'files[' + index + '][name]'"
                    >
                    <input type="text"
                           class="input-due line-el"
                           v-model="file.due"
                           v-datepicker
                           placeholder="Due date"
                           tabindex="-1"
                           :class="{'filled': file.due}"
                           :name="'files[' + index + '][due]'"
                    >
                </li>
            </ul>
        </form>
        <div class="text-right">
            <button type="button" class="btn btn-solid-green" @click="sendChecklist" :disabled="
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
            addAnotherFileAfter: function (file) {
                var fileIndex = _.indexOf(this.files, file);
                var newFile = {
                    name: '',
                    description: '',
                    due: ''
                };
                this.files.splice(fileIndex + 1, 0, newFile);
                this.$nextTick(function () {
                    $($('.single-file')[fileIndex + 1]).find('.input-file-name').focus();
                });
            },
            removeFile: function (file) {
                var fileIndex = _.indexOf(this.files, file);
                if (!file.name && fileIndex !== 0) {
                    this.files.splice(fileIndex, 1);
                    this.$nextTick(function () {
                        $($('.single-file')[fileIndex - 1]).find('.input-file-name').focus();
                    });
                }
            },
            goTo: function (direction, file) {
                var fileIndex = _.indexOf(this.files, file);
                if (direction === 'prev') {
                    $($('.single-file')[fileIndex - 1]).find('.input-file-name').focus();
                } else {
                    $($('.single-file')[fileIndex + 1]).find('.input-file-name').focus();
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


                self.$http.post('/checklist/make', {
                    recipients: self.checklistRecipients,
                    name: self.checklistName,
                    description: self.checklistDescription,
                    requested_files: self.validFiles
                }).then((response) => {
                    location.href = "/checklist/" + response.data;
                }, (response) => {
                    console.log(response);
                    vueValidation(response);
                    self.ajaxReady = true;
                });
            }
        },
        ready: function () {
            $(document).on('focus', '.input-file-name', function () {
                $(this).parent().addClass('is-focused');
            });

            $(document).on('blur', '.input-file-name', function () {
                $(this).parent().removeClass('is-focused');
            });

            vueGlobalEventBus.$on('created-account', function (user) {
                this.user = user;
                this.sendChecklist();
            }.bind(this));
        }
    };
</script>