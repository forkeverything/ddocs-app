// Create our global bus
var vueGlobalEventBus = new Vue();
new Vue({
    el: '#landing-start',
    data: {
        ajaxReady: true,
        emailExample: false,
        listMaker: false,
        checklistRecipient: '',
        checklistName: '',
        checklistDescription: '',
        editingName: false,
        files: [
            {
                name: '',
                description: '',
                due: '',
                required: 1
            }
        ],
        createdAccount: false
    },
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
            return this.checklistRecipient && this.checklistName && this.fileCount > 0;
        }
    },
    props: [],
    methods: {
        showEmailExample: function() {
            this.emailExample = true;
        },
        showListMaker: function() {
            this.listMaker = true;
        },
        showRegisterModal: function () {
            if (this.createdAccount) {
                this.sendChecklist();
            } else {
                vueGlobalEventBus.$emit('show-registration-modal');
            }
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
                due: '',
                required: 1
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
        toggleRequired: function (file) {
            file.required = file.required ? 0 : 1;
        },
        sendChecklist: function () {
            var self = this;
            vueClearValidationErrors();
            if (!self.ajaxReady) return;
            self.ajaxReady = false;
            $.ajax({
                url: '/checklist/make',
                method: 'POST',
                data: {
                    recipient: self.checklistRecipient,
                    name: self.checklistName,
                    description: self.checklistDescription,
                    requested_files: self.validFiles
                },
                success: function (data) {
                    location.href = "/checklist/" + data;
                },
                error: function (response) {
                    console.log(response);
                    vueValidation(response);
                    self.ajaxReady = true;
                }
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

        vueGlobalEventBus.$on('created-account', function() {
            this.createdAccount = true;
            this.sendChecklist();
        }.bind(this));
    }
});


//# sourceMappingURL=start.js.map
