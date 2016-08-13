Vue.component('account-overview', {
    name: 'AccountOverview',
    el: function() {
        return '#account-overview'
    },
    data: function() {
        return {
            ajaxReady: true,
            showCreditCardForm: false,
            cardError: '',
            waitingStripeResponse: false,
            ccName: '',
            ccNumber: '',
            ccExpMonth: '',
            ccExpYear: '',
            ccCVC: ''
        };
    },
    props: [],
    computed: {
        addCardButtonText: function() {
            if(this.waitingStripeResponse) return 'Processing...';
            return 'Add Card';
        },
        canSubmit: function() {
            return ! this.waitingStripeResponse && this.ccName && this.ccNumber && this.ccExpMonth && this.ccExpYear && this.ccCVC;
        }
    },
    methods: {
        toggleCreditCardForm: function() {
            this.showCreditCardForm = !this.showCreditCardForm;
        },
        processCard: function() {
            var self = this;

            // prevent request pile ups...
            if(self.waitingStripeResponse) return;
            self.waitingStripeResponse = true;

            var $form = $(self.$els.stripeForm);
            self.cardError = '';

            // Make request to stripe
            Stripe.card.createToken($form, function(status, response) {
                if(response.error) {
                    // Card error
                    self.cardError = response.error;
                    self.waitingStripeResponse = false;
                } else {
                    // Successfully got credit card token
                    $.ajax({
                        url: '/account/subscription',
                        method: 'POST',
                        data: {
                            'credit_card_token': response.id
                        },
                        success: function(data) {
                           // success
                            location.reload();
                        },
                        error: function(response) {
                            console.log(response);
                        }
                    });
                }
            });
        },
        cancelSubscription: function() {
            var self = this;
            if(!self.ajaxReady) return;
            self.ajaxReady = false;
            $.ajax({
                url: '/account/subscription',
                method: 'DELETE',
                success: function(data) {
                   // success
                    location.reload();
                   self.ajaxReady = true;
                },
                error: function(response) {
                    console.log(response);
                    self.ajaxReady = true;
                }
            });
        }
    },
    events: {

    },
    ready: function() {
        Stripe.setPublishableKey($('meta[name="stripe-key"]').attr('content'));
    }
});
Vue.component('checklist-all', fetchesFromEloquentRepository.extend({
    name: 'checklistAll',
    el: function () {
        return '#checklist-all'
    },
    data: function () {
        return {
            ajaxReady: true,
            hasFilters: false,
            requestUrl: "checklist/get",
            checklists: []
        };
    },
    props: [],
    computed: {},
    methods: {},
    events: {},
    ready: function () {
        var self = this;
        this.$watch('response', function (response) {
            self.checklists = $.map(_.omit(response.data, 'query_parameters'), function (checklist, index) {
                return checklist;
            });
        });
    }
}));
Vue.component('checklist-make', {
    name: 'checklistMake',
    el: function () {
        return '#checklist-make'
    },
    data: function () {
        return {
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
            ]
        };
    },
    props: [],
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
        },
        makeListButtonText: function () {
            if (this.ajaxReady) return 'Create List';
            return 'Saving...';
        }
    },
    methods: {
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
            vueClearValidationErrors(self);
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
                    vueValidation(response, self);
                    self.ajaxReady = true;
                }
            });
        }
    },
    events: {},
    ready: function () {
        $(document).on('focus', '.input-file-name', function () {
            $(this).parent().addClass('is-focused');
        });

        $(document).on('blur', '.input-file-name', function () {
            $(this).parent().removeClass('is-focused');
        });
    }
});
Vue.component('checklist-single', fetchesFromEloquentRepository.extend({
    name: 'checklistSingle',
    el: function () {
        return '#checklist-single'
    },
    data: function () {
        return {
            ajaxReady: true,
            hasFilters: true,
            filterOptions: [
                {
                    value: 'required',
                    label: 'Requirement'
                },
                {
                    value: 'version',
                    label: 'Version'
                },
                {
                    value: 'due',
                    label: 'Due Date'
                },
                {
                    value: 'status',
                    label: 'Status'
                }
            ],
            files: [],
            fileWithExpandedDetails: '',           // Holds a file object
            expandedView: '',                      // 'history', 'reject'
            reason: '',
            numReceived: '',
            emailToTurnOffNotifications: '',
            showTurnOffNotificationsPanel: false
        };
    },
    props: ['checklist-hash', 'recipient-notifications'],
    computed: {
        requestUrl: function () {
            return '/checklist/' + this.checklistHash + '/files';
        },
        receivedFilesPercentage: function() {
            if(! this.response.total) return 0;
            return (100 * this.numReceived / this.response.total).toFixed(2);
        }
    },
    methods: {
        getUploadDate: function (file) {
            return moment(file.created_at).format('DDMMYYYY');
        },
        uploadFile: function (file, $event) {
            var fd = new FormData();
            var uploadedFile = $event.srcElement.files[0];

            fd.append('file', uploadedFile);

            var self = this;
            if (!self.ajaxReady) return;
            self.ajaxReady = false;
            $.ajax({
                url: '/file/' + file.id,
                method: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                xhr: function () {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) myXhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            var max = e.total;
                            var current = e.loaded;
                            var progress = Math.round((current * 100) / max);
                            $('#input-file-' + file.id).siblings('.progress').children('.progress-bar').css(
                                'width',
                                progress + '%'
                            ).attr('aria-valuenow', progress).children('.sr-only').html(progress + '%');
                        }
                    }, false);
                    return myXhr;
                },
                success: function (file) {
                    self.replaceFile(file);
                    self.numReceived ++;

                    self.ajaxReady = true;
                },
                error: function (response) {
                    console.log(response);
                    self.ajaxReady = true;
                }
            });
        },
        replaceFile: function (updatedFileModel) {
            var self = this;
            var index = _.indexOf(self.files, _.find(self.files, {id: updatedFileModel.id}));
            self.files.splice(index, 1, updatedFileModel);
        },
        updateProgress: function (e) {
            if (e.lengthComputable) {
                var max = e.total;
                var current = e.loaded;
                var Percentage = Math.round((current * 100) / max);
                if (Percentage >= 100) {
                    // process completed
                }
            }
        },
        expandFileSection: function (file, section) {
            this.reason = '';
            this.fileWithExpandedDetails = file;
            this.expandedView = section;
        },
        fileExpanded: function (file) {
            return this.fileWithExpandedDetails === file;
        },
        hideDetailsSection: function () {
            this.fileWithExpandedDetails = '';
        },
        rejectFile: function (file) {
            var self = this;
            if (!self.ajaxReady) return;
            self.ajaxReady = false;
            $.ajax({
                url: '/file/' + file.id + '/reject',
                method: 'POST',
                data: {
                    "reason": self.reason
                },
                success: function (file) {
                    // success
                    self.reason = '';
                    self.replaceFile(file);
                    self.numReceived --;
                    self.ajaxReady = true;
                },
                error: function (response) {
                    console.log(response);
                    self.ajaxReady = true;
                }
            });
        },
        toggleConfirmTurnOffNotifications: function() {
            this.showTurnOffNotificationsPanel = ! this.showTurnOffNotificationsPanel;
        },
        turnOffNotifications: function() {

            var self = this;
            vueClearValidationErrors(self);
            if(!self.ajaxReady) return;
            self.ajaxReady = false;
            $.ajax({
                url: '/checklist/' + self.checklistHash + '/turn_off_notifications',
                method: 'POST',
                data: {
                    "email": self.emailToTurnOffNotifications
                },
                success: function(data) {
                   // success
                   self.recipientNotifications = 0;
                    self.showTurnOffNotificationsPanel = false;
                   self.ajaxReady = true;
                },
                error: function(response) {
                    console.log(response);
                    
                    vueValidation(response, self);
                    self.ajaxReady = true;
                }
            });
        }
    },
    events: {},
    ready: function () {

        var self = this;

        // Use a watcher to parse out the stuff we need to be reactive because Vue can't watch
        // object properties.
        this.$watch('response', function (response) {
            self.files = $.map(_.omit(response.data, 'query_parameters'), function (file, index) {

                // Add custom props for UI
                // file.expanded = false;

                return file;
            });
            self.numReceived = self.params.num_received_files;
        });

        $(document).on('click', '.button-upload', function (e) {
            e.preventDefault();
            $('#input-file-' + $(this).data('file')).click();
        });

    }
}));
//# sourceMappingURL=pages.js.map
