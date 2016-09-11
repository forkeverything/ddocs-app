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
            <ul class="list-files list-unstyled">
                <li v-for="(index, file) in files" class="single-file">

                    <span class="icon-file line-el">
                    <i class="fa fa-file"></i>
                    </span>

                    <input type="text"
                           class="form-control input-file-name line-el"
                           v-model="file.name"
                           placeholder="File name"
                           @keyup.enter="addFile"
                           @keydown.delete="removeFile"
                           @keydown.up.prevent.stop="pressedArrow('prev')"
                           @keyup="searchForFileName($event, file, index)"
                           @keydown.down.prevent.stop="pressedArrow('next')"
                           :name="'files[' + index + '][name]'"
                           @focus="focusNameInput(index, file)"
                    >

                        <ul class="list-name-options list-unstyled" v-show="focusedFileIndex === index && fileNameOptions.length > 0">
                            <li v-for="(index, name) in fileNameOptions"
                                track-by="$index"
                                tabIndex="-1"
                                class="single-name"
                                @mouseover="selectOption(index)"
                                @click="addFile"
                                :class="{
                                    'is-selected': selectedOptionIndex === index
                                }"
                            >
                                {{ name }}
                            </li>
                        </ul>

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
            <button type="button" class="btn btn-primary" @click="sendChecklist" :disabled="
                    ! canSendChecklist">{{ submitButtonText }}
            </button>
        </div>
    </div>
</template>
<script>

    let xhr_requests = [];

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
                focusedFileIndex: '',
                selectedOptionIndex: '',
                fileNameOptions: []
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
            selectOption: function(index) {
                this.selectedOptionIndex = index;
            },
            focusNameInput: function(index, file) {
                this.clearSearchRequests();
                this.focusedFileIndex = index;
            },
            searchForFileName: function(event, file, index) {
                if(event.key.length === 1 || event.key === "Backspace") {
                    // input a character or backspaced
                    this.fetchFileNames(file.name, index);
                }
            },
            clearSearchRequests: function() {
                this.fileNameOptions = [];
                for (var i = 0; i < xhr_requests.length; i++) {
                    xhr_requests.shift().abort();
                }
            },
            fetchFileNames: _.debounce(function(searchString, index) {
                console.log(searchString);
                if(! searchString) return;
                this.$http.get('/files?name_only=1&limit=5&search=' + searchString, {
                            before: (xhr) => {
                                // Abort old unfinished requests
                                this.clearSearchRequests();
                                // Add current request to the queue
                                xhr_requests.push(xhr);
                            }
                        })
                        .then((response) => {
                            // If we've moved on to a diff file, results useless
                            if(index !== this.focusedFileIndex) return ;
                            // Success
                            let results = JSON.parse(response.data);
                            // If our search string isn't in results, we'll make it the first option
                            if(results.map((name) => name.toLowerCase()).indexOf(searchString.toLowerCase()) === -1) results.unshift(searchString);
                            // select first option
                            this.selectedOptionIndex = 0;
                            // append results
                            this.fileNameOptions = results;
                        });
            }, 200),
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
            addFile: function() {
                let selectedOption = this.fileNameOptions[this.selectedOptionIndex];
                let currentFileName = this.files[this.focusedFileIndex].name;

                if(! selectedOption && ! currentFileName) return;

                if(selectedOption) this.files[this.focusedFileIndex].name = selectedOption;

                var newFile = {
                    name: '',
                    description: '',
                    due: ''
                };

                this.files.splice(this.focusedFileIndex + 1, 0, newFile);
                this.$nextTick(function() {
                    this.goTo('next');
                });
            },
            removeFile: function () {
                if (!this.files[this.focusedFileIndex].name && this.focusedFileIndex !== 0) {
                    this.files.splice(this.focusedFileIndex, 1);
                    this.$nextTick(function () {
                        $($('.single-file')[this.focusedFileIndex - 1]).find('.input-file-name').focus();
                    });
                }
            },
            pressedArrow: function(direction) {
                let optionIndexToSelect = (direction === 'prev') ? this.selectedOptionIndex - 1 : this.selectedOptionIndex + 1 ;
                if(this.fileNameOptions && this.fileNameOptions[optionIndexToSelect]) {
                    this.selectOption(optionIndexToSelect);
                } else {
                    this.goTo(direction);
                }
            },
            pressedArrowUp: function() {
                if(this.fileNameOptions && this.fileNameOptions[this.selectedOptionIndex - 1]) {
                    // Have options and there is a next option - select it
                    this.selectOption(this.selectedOptionIndex - 1);
                } else {
                    this.goTo('prev');
                }
            },
            pressedArrowDown: function() {
                if(this.fileNameOptions && this.fileNameOptions[this.selectedOptionIndex + 1]) {
                    // Have options and there is a next option - select it
                    this.selectOption(this.selectedOptionIndex + 1);
                } else {
                    this.goTo('next');
                }
            },
            goTo: function (direction) {
                if (direction === 'prev') {
                    $($('.single-file')[this.focusedFileIndex - 1]).find('.input-file-name').focus();
                } else {
                    $($('.single-file')[this.focusedFileIndex + 1]).find('.input-file-name').focus();
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