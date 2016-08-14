// bus for registration
var vueRegistrationEventBus = new Vue();

Vue.component('registration-modal', {
    name: 'RegistrationModal',
    template: '<div id="modal-register" class="modal" role="dialog" v-el:modal-register>' +
    '<div class="modal-dialog">' +
    '<div class="modal-content">' +
    '<div class="modal-header">' +
    '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
    '<h2 class="modal-title text-center">Create Account</h2>' +
    '</div>' +
    '<div class="modal-body">' +
    '<form-errors :event-bus="bus"></form-errors>' +
    '<form>' +
    '<div class="form-group row">' +
    '<label class="col-xs-4 control-label text-right">Name</label>' +
    '<div class="col-xs-6">' +
    '<input type="text" class="form-control" name="name" v-model="registerName">' +
    '</div>' +
    '</div>' +
    '<div class="form-group row">' +
    '<label class="col-xs-4 control-label text-right">Email</label>' +
    '<div class="col-xs-6">' +
    '<input type="email" class="form-control" name="email" v-model="registerEmail">' +
    '</div>' +
    '</div>' +
    '<div class="form-group row">' +
    '<label class="col-xs-4 control-label text-right">Password</label>' +
    '<div class="col-xs-6">' +
    '<input type="password" class="form-control" name="password" v-model="registerPassword">' +
    '</div>' +
    '</div>' +
    '<div class="form-group row">' +
    '<label class="col-xs-4 control-label text-right">Confirm Password</label>' +
    '<div class="col-xs-6">' +
    '<input type="password" class="form-control" name="password_confirmation" v-model="registerPasswordConfirmation">' +
    '</div>' +
    '</div>' +
    '</form>' +
    '</div>' +
    '<div class="modal-footer">' +
    '<button type="button" class="btn btn-outline-grey btn-space" data-dismiss="modal">Cancel</button>' +
    '<button type="button" class="btn btn-solid-green" @click="createAccount">{{ registerButtonText }}</button>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>',
    data: function () {
        return {
            ajaxReady: true,
            bus: vueRegistrationEventBus,
            registerName: '',
            registerEmail: '',
            registerPassword: '',
            registerPasswordConfirmation: ''
        };
    },
    props: [],
    computed: {
        registerButtonText: function() {
            if(this.ajaxReady) return 'Save';
            return 'Saving...';
        }
    },
    methods: {
        createAccount: function () {
            var self = this;
            self.registerButtonText = 'Saving...';
            vueClearValidationErrors(vueRegistrationEventBus);
            if (!self.ajaxReady) return;
            self.ajaxReady = false;
            $.ajax({
                url: '/register',
                method: 'POST',
                data: {
                    "name": self.registerName,
                    "email": self.registerEmail,
                    "password": self.registerPassword,
                    "password_confirmation": self.registerPasswordConfirmation
                },
                success: function (data) {
                    $(self.$els.modalRegister).modal('hide');
                    vueGlobalEventBus.$emit('created-account');
                },
                error: function (response) {
                    console.log(response);

                    vueValidation(response, vueRegistrationEventBus);
                    self.ajaxReady = true;
                }
            });
        }
    },
    ready: function () {
        $(this.$els.modalRegister).modal({
            backdrop: 'static',
            show: false
        });

        vueGlobalEventBus.$on('show-registration-modal', function () {
            $(this.$els.modalRegister).modal('show');
        }.bind(this));
    }
});
// Create our global bus
var vueGlobalEventBus = new Vue();
new Vue({
    el: '#landing',
    data: {
        ajaxReady: true,
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


//# sourceMappingURL=main.js.map
