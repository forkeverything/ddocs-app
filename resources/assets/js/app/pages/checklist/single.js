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